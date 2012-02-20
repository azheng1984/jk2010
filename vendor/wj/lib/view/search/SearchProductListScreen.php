<?php
class SearchProductListScreen {
  private static $keywordList;
  private static $hasCategory;

  public static function render() {
    self::$keywordList =
      explode(' ', SegmentationService::execute($GLOBALS['QUERY']['name']));
    $index = 0;
    self::$hasCategory = isset($GLOBALS['CATEGORY']);
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

  private static function renderProduct($id) {
    $product = DbProduct::get($id);
    $merchant = DbMerchant::get($product['merchant_id']);
    $excerption = self::highlight(self::excerpt($product['property_list']));
    $href = self::getProductUri(
      $merchant['product_uri_format'], $product['uri_argument_list']
    );
    $tagList = self::getTagList($product);
    echo '<td><div class="image"><a href="',
      $href, '" target="_blank" rel="nofollow">',
      '<img alt="', $product['title'], '" src="http://img.dev.huobiwanjia.com/',
      $product['id'], '.jpg"/></a></div>',//image
      '<h3><a href="', $href, '" target="_blank" rel="nofollow">',
      self::highlight($product['title']), '</a></h3>',//title
      '<div class="price">&yen;<span>',
      $product['lowest_price_x_100']/100, '</span></div>';//price
    if ($excerption !== '') {
      echo '<p>', $excerption, '&hellip;</p>';//excerption
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
      $result[] = '<a href="'.urlencode('品牌='.$product['brand_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">品牌：'
        .$product['brand_name'].'</a>';
    }
    return $result;
  }

  private static function getProductUri($format, $argumentList) {
    return $format.$argumentList;
  }

  private static function excerpt($propertyList) { //TODO
    $list = array();
    foreach (self::$keywordList as $keyword) {
      preg_match('{\n.*'.$keyword.'.*}', "\n".$propertyList, $matches);
      $property = substr($matches[0], 1);
      if (isset($matches[0])) {
        $list[$matches[0]] = true;
      }
    }
    $list = array_keys($list);
    return implode('。', $list);
  }

  private static function reduceExcerption($string) {
    
  }

  private static function highlight($text) { //TODO
    $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
    foreach (self::$keywordList as $keyword) {
      if (strpos($text, $keyword) !== false) {
        $text = str_replace($keyword, "<span>$keyword</span>", $text);
      }
    }
    return $text;
  }
}