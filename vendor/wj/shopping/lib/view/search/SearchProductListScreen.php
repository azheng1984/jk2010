<?php
class SearchProductListScreen {
  private static $keywordList;
  private static $recognitionList;
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
    $merchant = self::getMerchant($product['merchant_id']);
    $tagList = self::initializeTagList($product);
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
      echo self::highlight(
        SearchExcerptionScreen::excerpt(
          self::$tagLinkList, self::$tagTextList, $product['property_list']
        )
      );
    }
    if ($product['recognition_id'] !== null) {
      $recognition = self::getRecognition($product['recognition_id']);
      echo '<a class="unique" href="/+-'.urlencode($recognition['name']), '/',
        $GLOBALS['QUERY_STRING'], '" rel="nofollow">',
        $recognition['product_amount'], ' 个同款商品</a>';
    }
    echo '<div class="merchant">', $merchant['name'], '</div></td>';//merchant
  }

  private static function getMerchant($id) {
    if (isset(self::$merchantList[$id]) === false) {
      self::$merchantList[$id] =
        Db::getRow('SELECT * FROM merchant WHERE id = ?', $id);
    }
    return self::$merchantList[$id];
  }

  private static function getRecognition($id) {
    if (isset(self::$recognitionList[$id]) === false) {
      self::$recognitionList[$id] =
        Db::getRow('SELECT * FROM recognition WHERE id = ?', $id);
    }
    return self::$recognitionList[$id];
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