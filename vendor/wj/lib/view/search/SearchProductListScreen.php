<?php
class SearchProductListScreen {
  private static $merchantList;
  private static $cutList;
  private static $hasCategory;
  private static $keywordList;

  public static function render() {
    self::initialize();
    $index = 0;
    echo '<table><tr>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      if ($index % 4 === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      ++$index;
      self::renderProduct($id);
    }
    if ($index % 4 !== 0 && $index > 4) {
      $colspan = 4 - $index % 4;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table>';
  }

  private static function initialize() {
    self::$merchantList = array();
    self::$cutList = array();
    self::$hasCategory = isset($GLOBALS['CATEGORY']);
    self::$keywordList = explode(' ',
      SegmentationService::execute($GLOBALS['QUERY']['name']));
    if (count(self::$keywordList) < 2) {
      return;
    }
    usort(self::$keywordList, function($x, $y) {
      return strlen($y) - strlen($x);
    });
    self::$keywordList = array_unique(self::$keywordList);
  }

  private static function renderProduct($id) {
    $product = DbProduct::get($id);
    $merchant = self::getMerchant($product['merchant_id']);
    $href = self::getProductUri(
      $merchant['product_uri_format'], $product['uri_argument_list']
    );
    echo '<td><div class="image"><a href="',
      $href, '" target="_blank" rel="nofollow">',
      '<img alt="', $product['title'], '" src="',
      self::getImageUri($product), '"/></a></div>',//image
      '<h3><a href="', $href, '" target="_blank" rel="nofollow">';
    $title = $product['title'];
    if (mb_strlen($title, 'UTF-8') > 60) {
      $title = mb_substr($title, 0, 60, 'UTF-8');
    }
    echo self::highlight($title), '</a></h3>',//title
      '<div class="price">¥<span>',
      $product['lowest_price_x_100']/100, '</span></div>';//price
    if ($product['property_list'] !== null) {
      $excerption = self::highlight(self::excerpt($product['property_list']));
      echo '<p>', $excerption, '</p>';//excerption
    }
    $tagList = self::getTagList($product);
    if ($tagList !== '') {
      echo '<div class="tag_list">', $tagList, '</div>';
    }
    echo '<div class="merchant">', $merchant['name'], '</div>',//merchant
      '</td>';
  }

  private static function getMerchant($id) {
    if (isset(self::$merchantList[$id]) === false) {
      self::$merchantList[$id] = DbMerchant::get($id);
    }
    return self::$merchantList[$id];
  }

  private static function getImageUri($product) {
    if ($product['image_db_index'] === null) {
      return 'http://dev.huobiwanjia.com/+/no_image.'
        .Asset::getMd5('no_image').'.jpg';
    }
    $imageUri = 'http://img.dev.huobiwanjia.com/'.$product['id'];
    if ($product['image_md5'] !== null) {
      $imageUri .= '.'.$product['image_md5'];
    }
    return $imageUri.'.jpg';
  }

  private static function getTagList($product) {
    $result = '';
    if (self::$hasCategory === false && $product['category_name'] !== null) {
      $result .= '<a href="'.urlencode($product['category_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">分类: '
        .$product['category_name'].'</a>';
    }
    if (self::$hasCategory === true && $product['brand_name'] !== null
      && isset($GLOBALS['PROPERTY_LIST']['品牌']) === false) {
      $result .= '<a href="'.self::getBrandPath($product['brand_name'])
        .'" rel="nofollow">品牌: '.$product['brand_name'].'</a>';
    }
    if ($product['query_name'] !== null) {
      $result .= '<a href="/+-'.urlencode($product['query_name'])
      .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">同款</a>';
    }
    return $result;
  }

  private static function getBrandPath($brandName) {
    $path = urlencode('品牌='.$brandName).'/'.$GLOBALS['QUERY_STRING'];
    if (count($GLOBALS['PROPERTY_LIST']) > 0
      && strpos($GLOBALS['PATH_SECTION_LIST'][3], '"') === false) {
      $path = $GLOBALS['PATH_SECTION_LIST'][3].'&'.$path;
    }
    return $path;
  }

  private static function getProductUri($format, $argumentList) {
    return vsprintf($format, $argumentList);
  }

  private static function excerpt($text) {
    $list = array();
    $isLink = true;
    $orignalAmount = 0;
    foreach (explode("\n", $text) as $propertyText) {
      if ($propertyText === '') {
        $isLink = false;
        continue;
      }
      $list[$propertyText] = $isLink;
      ++$orignalAmount;
    }
    if (mb_strlen($text, 'UTF-8') > 60) {
      $list = self::reducePropertyList($list);
    }
    $linkList = array();
    $textList = array();
    $linkAmount = 0;
    $textAmount = 0;
    $hasCategory = isset($GLOBALS['CATEGORY']);
    $currentPropertyList = array();//TODO:build current property list
    //TODO:refactor
    foreach ($list as $propertyText => $isLink) {
      if ($isLink) {
        $tmp = explode('：', $propertyText, 2);
        if (count($tmp) === 2) {
          list($keyName, $valueName) = $tmp;
          if (isset($currentPropertyList[$keyName])) {
            if (strpos($valueName, '；') !== false) {
              $list = explode('；', $valueName);
              $list2 = array();
              foreach ($list as $item) {
                if (isset($currentPropertyList[$keyName][$item])) {
                  continue;
                }
                $list2[] = $item;
              }
              if (count($list2) === 0) {
                continue;
              }
              $propertyText = $keyName.'：'.implode('；', $list2);
            } else {
              if (isset($currentPropertyList[$keyName][$valueName])) {
                continue;
              }
            }
          }
        }
      }
      if ($hasCategory && $isLink) {
        $linkList[] = $propertyText;
        ++$linkAmount;
        continue;
      }
      $textList[] = $propertyText;
      ++$textAmount;
    }
    $amount = $linkAmount + $textAmount;
    $isFull = $amount === $orignalAmount;
    $count = 0;
    $result = '';
    if ($linkAmount !== 0) {
      $result .= '<span class="link_list">';
      foreach ($linkList as $item) {
        ++$count;
        $end = '。';
        if (isset(self::$cutList[$item])
          || ($count === $amount && $isFull === false)) {
          $end = '…';
        }
        $result .= $item.$end;
      }
      $result .= '</span>';
    }
    foreach ($textList as $item) {
      ++$count;
      $end = '。';
      if (isset(self::$cutList[$item])
        || ($count === $amount && $isFull === false)) {
        $end = '…';
      }
      $result .= $item.$end;
    }
    return $result;
  }

  private static function reducePropertyList($list) {
    $length = 0;
    $result = array();
    $matchList = array();
    foreach (self::$keywordList as $keyword) {
      foreach ($list as $propertyText => $isLink) {
        if (strpos($propertyText, $keyword) === false) {
          continue;
        }
        if (isset($result[$propertyText]) === false) {
          $result[$propertyText] = $isLink;
          $propertyLength = mb_strlen($propertyText, 'UTF-8');
          $matchList[] = array($propertyText, $isLink, $propertyLength);
          $length += $propertyLength;
        }
        break;
      }
    }
    if ($length < 60) {
      return self::increaseExcerption($list, $result, $length);
    }
    for ($index = count($matchList) - 1; $index > -1; --$index) {
      $item = $matchList[$index];
      $propertyText = $item[0];
      $isLink = $item[1];
      $propertyLength = $item[2];
      if ($propertyLength > 12) {
        $cut = self::cutProperty($propertyText, $isLink, $propertyLength);
      }
      if ($cut !== null) {
        $matchList[$index] = array($cut[0], $isLink, $cut[1]);
        $length -= $propertyLength - $cut[1];
        self::$cutList[$cut[0]] = true;
      }
      if ($length < 60) {
        break;
      }
    }
    $length = 0;
    $result = array();
    foreach ($matchList as $item) {
      if ($length + $item[2] > 60 && $length > 0) {
        break;
      }
      $result[$item[0]] = $item[1];
      $length += $item[2];
    }
    return $result;
  }

  private static function cutProperty($text, $isLink, $length) {
    if ($isLink === false) {
      return array(mb_substr($text, 0, 12, 'UTF-8'), 12);
    }
    $endPosition = mb_strpos($text, '；', 0, 'UTF-8');
    if ($endPosition === false) {
      return;
    }
    for (;;) {
      $position = mb_strpos($text, '；', $endPosition, 'UTF-8');
      if ($position === false || $position > 12) {
        break;
      }
      $endPosition = $position;
    }
    return array(mb_substr($text, 0, $endPosition, 'UTF-8'), $endPosition);
  }

  private static function increaseExcerption($list, $result, $length) {
    if (count($list) === count($result)) {
      return $result;
    }
    foreach ($list as $propertyText => $isLink) {
      if (isset($result[$propertyText])) {
        continue;
      }
      $propertyLength = mb_strlen($propertyText, 'UTF-8');
      $length += $propertyLength;
      if ($length > 60) {
        break;
      }
      $result[$propertyText] = $isLink;
    }
    return $result;
  }

  private static function highlight($text) {
    $positionList = array();
    foreach (self::$keywordList as $keyword) {
      $length = strlen($keyword);
      $offset = 0;
      while (false !== ($offset = strpos($text, $keyword, $offset))) {
        if (isset($positionList[$offset]) === false) {
          $positionList[$offset] = $length;
        }
        $offset = $offset + $length;
      }
    }
    $amount = count($positionList);
    if ($amount === 0) {
      return $text;
    }
    if ($amount > 1) {
      ksort($positionList);
    }
    $result = '';
    $offset = 0;
    foreach ($positionList as $start => $length) {
      $next = $start + $length;
      if ($next <= $offset) {
        continue;
      }
      if ($start < $offset) {
        $length = $length + $start - $offset;
        $start = $offset;
      }
      while (isset($positionList[$next])) {
        $length += $positionList[$next];
        $next += $positionList[$next];
      }
      $result .= substr($text, $offset, $start - $offset).'<span>'
        .substr($text, $start, $length).'</span>';
      $offset = $next;
    }
    if ($offset < strlen($text)) {
      $result .= substr($text, $offset);
    }
    return $result;
  }
}