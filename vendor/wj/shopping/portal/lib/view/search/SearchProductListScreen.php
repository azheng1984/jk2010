<?php
class SearchProductListScreen {
  private static $keywordList;
  private static $queryTagList;
  private static $merchantList;
  private static $hasCategory;
  private static $tagLinkList;
  private static $tagTextList;

  public static function render() {
    self::initialize();
    SearchExcerptionScreen::initialize(self::$keywordList);
    $index = 0;
    echo '<table><tr>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      if ($index % 5 === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      ++$index;
      self::renderProduct($id);
    }
    if ($index % 5 !== 0 && $index > 5) {
      $colspan = 5 - $index % 5;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table>';
  }

  private static function initialize() {
    self::$merchantList = array();
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
    $product = Db::getRow('SELECT * FROM product WHERE id = ?', $id);
    $merchant = array('name' => '京东商城');//self::getMerchant($product['merchant_id']);
    $tagList = array();//self::initializeTagList($product);
//     $href = self::getProductUri(
//       $merchant['product_uri_format'],
//       explode("\n", $product['merchant_uri_argument_list'])
//     );
    $href = 'http://www.360buy.com/product/'.$product['uri_argument_list'].'.html';
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
      $product['price_from_x_100']/100, '</span></div>';//price
//     if ($product['property_list'] !== null) {
//       echo self::highlight(
//         SearchExcerptionScreen::excerpt(
//           self::$tagLinkList, self::$tagTextList, $product['property_list']
//         )
//       );
//     }
//     if ($product['query_tag_id'] !== null) {
//       $queryTag = self::getQueryTag($product['query_tag_id']);
//       echo '<a class="query_tag" href="/+-'.urlencode($queryTag['name']),
//         '/', $GLOBALS['QUERY_STRING'], '" rel="nofollow">',
//         $queryTag['product_amount'], ' 个商家</a>';
//     }
    echo '<div class="merchant">', $merchant['name'], '</div></td>';//merchant
  }

  private static function getMerchant($id) {
    if (isset(self::$merchantList[$id]) === false) {
      self::$merchantList[$id] =
        Db::getRow('SELECT * FROM merchant WHERE id = ?', $id);
    }
    return self::$merchantList[$id];
  }

  private static function getQueryTag($id) {
    if (isset(self::$queryTagList[$id]) === false) {
      self::$queryTagList[$id] =
        Db::getRow('SELECT * FROM query_tag WHERE id = ?', $id);
    }
    return self::$queryTagList[$id];
  }

  private static function getImageUri($product) {
    if ($product['image_path'] === null) {
      return 'http://dev.huobiwanjia.com/+/no_image.'
        .Asset::getMd5('no_image').'.jpg';
    }
    $imageUri = 'http://img.dev.huobiwanjia.com/'.$product['image_path'].'/'.$product['id'];
    if ($product['image_digest'] !== null) {
      $imageUri .= '.'.$product['image_digest'];
    }
    return $imageUri.'.jpg';
  }

  private static function initializeTagList($product) {
    self::$tagLinkList = array();
    self::$tagTextList = array();
    if (self::$hasCategory === false && $product['category_name'] !== null) {
      self::$tagLinkList[] = '分类: <span class="value">'
        .$product['category_name'].'</span>';
    }
    if (self::$hasCategory === false && $product['brand_name'] !== null) {
      self::$tagTextList[] = '品牌: '.$product['brand_name'];
      return;
    }
    if (self::$hasCategory === true && $product['brand_name'] !== null
      && isset($GLOBALS['PROPERTY_LIST']['品牌']) === false) {
      self::$tagLinkList[] = '品牌: <span class="value">'
        .$product['brand_name'].'</span>';
    }
  }

  private static function getBrandPath($brandName) {
    $path = '%E5%93%81%E7%89%8C='.urlencode($brandName)
      .'/'.$GLOBALS['QUERY_STRING'];
    if (isset($GLOBALS['PROPERTY_LIST'])
      && strpos($GLOBALS['PATH_SECTION_LIST'][3], '"') === false) {
      $path = $GLOBALS['PATH_SECTION_LIST'][3].'&'.$path;
    }
    return $path;
  }

  private static function getProductUri($format, $argumentList) {
    return vsprintf($format, $argumentList);
  }

  private static function highlight($text) {
    if ($text === null || $text === '') {
      return '';
    }
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