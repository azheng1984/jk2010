<?php
class SearchProductListScreen {
  private static $keywordList;
  private static $hasCategory;

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
    if ($index % 4 !== 0) {
      $colspan = 4 - $index % 4;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, ' class="empty"></td>';
    }
    echo '</tr></table>';
  }

  private static function initialize() {
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
    $merchant = DbMerchant::get($product['merchant_id']);
    $excerption = self::highlight(self::excerpt($product['property_list']));
    $href = self::getProductUri(
      $merchant['product_uri_format'], $product['uri_argument_list']
    );
    $imageUri = 'http://img.dev.huobiwanjia.com/'.$product['id'];
    if ($product['image_md5']) {
      $imageUri .= '.'.$product['image_md5'];
    }
    $imageUri .= '.jpg';
    $tagList = self::getTagList($product);
    echo '<td><div class="image"><a href="',
      $href, '" target="_blank" rel="nofollow">',
      '<img alt="', $product['title'], '" src="', $imageUri, '"/></a></div>',//image
      '<h3><a href="', $href, '" target="_blank" rel="nofollow">',
      self::highlight($product['title']), '</a></h3>',//title
      '<div class="price">&yen;<span>',
      $product['lowest_price_x_100']/100, '</span></div>';//price
    if ($excerption !== '') {
      echo '<p>', $excerption, '</p>';//excerption
    }
    if (count($tagList) !== 0) {
      echo '<div class="tag_list">', implode(' ', $tagList), '</div>';
    }
    echo '<div class="merchant">', $merchant['name'], '</div>',//merchant
      '</td>';
  }

  private static function getTagList($product) {
    $result = array();
    if ($product['query_name'] !== null) {
      $result[] = '<a href="/+-'.urlencode($product['query_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">同款</a>';
    }
    if (self::$hasCategory === false && $product['category_name'] !== null) {
      $result[] = '<a href="'.urlencode($product['category_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">分类：'
        .$product['category_name'].'</a>';
      return $result;
    }
    if (self::$hasCategory === true && $product['brand_name'] !== null) {
      $result[] = '<a href="'.urlencode('品牌='.$product['brand_name']) //TODO: path
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">品牌：'
        .$product['brand_name'].'</a>';
    }
    return $result;
  }

  private static function getProductUri($format, $argumentList) {
    return vsprintf($format, $argumentList);
  }

  private static function excerpt($text) {
    if (mb_strlen($text) < 60) {
      return $text;
    }
    $propertyList = explode('\n', $text);
    $matchList = array();
    $length = 0;
    foreach (self::$keywordList as $keyword) {
      $type = 'link';
      foreach ($propertyList as $propertyText) {
        if ($propertyText === '') {
          $type = 'text';
          continue;
        }
        if (strpos($propertyText, $keyword) === false) {
          continue;
        }
        if (isset($matchList[$propertyText]) === false) {
          $propertyLength = mb_strlen($propertyText);
          $matchList[$propertyText] = array($propertyLength, $type);
          $length += $propertyLength;
        }
      }
    }
    if ($length === 0) {
      return '';
    }
    
    if ($length > 60) {
      $matchList = self::reduceExcerption($matchList, $length);
    }
    $matchList = self::increaseExcerption($propertyList, $matchList, $length);
    $textList = array();
    $linkList = array();
    foreach ($matchList as $text => $metaList) {
      if ($metaList[1] === 'text') {
        $textList[] = $text;
        continue;
      }
      $linkList[] = $text;
    }
    $end = '。';
    if (count($matchList) < count($propertyList)) {
      $end = '&hellip;';
    }
    $result = '';
    $hasTextList = count($textList) === 0;
    if (count($linkList) !== 0) {
      $result = '<span class="link">'.implode('。', array_keys($linkList));
    }
    if (count($textList) === 0) {
      return $result.$end.'</span>';
    }
    return $result.'。</span>'.implode('。', array_keys($linkList)).$end;
  }

  private static function reduceExcerption($matchList, $length) {
    foreach ($matchList as $text => $metaList) {
      if ($metaList[0] > 15 && $metaList[1] === 'text') {
        $text = mb_substr($text, 0, 15).'&hellip;';
        $length -= 15 - $metaList[0];
        if ($length < 60) {
          break;
        }
      }
    }
    $amount = count($matchList);
    while ($length > 60 && $amount > 1) {
      $length -= array_pop($matchList);
      --$amount;
    }
    return $matchList;
  }

  private static function increaseExcerption(
    $propertyList, $matchList, $length
  ) {
    $type = 'link';
    foreach ($propertyList as $propertyText) {
      if ($propertyText === '') {
        $type = 'text';
        continue;
      }
      if (isset($matchList[$propertyText])) {
        continue;
      }
      $propertyLength = mb_strlen($propertyText);
      $length += $propertyLength;
      if ($length < 60) {
        break;
      }
      $matchList[$propertyText] = array($propertyLength, $type);
    }
    return $matchList;
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
      if ($start < $offset) {
        continue;
      }
      $result .= substr($text, $offset, $start - $offset).'<span>'
        .substr($text, $start, $length).'</span>';
      $offset = $start + $length;
    }
    if ($offset !== strlen($text)) {
      $result .= substr($text, $offset);
    }
    return $result;
  }
}