<?php
class SearchProductListScreen {
  public static function render() {
    $keywordList =
      explode(' ', SegmentationService::execute($GLOBALS['QUERY']['name']));
    $index = 0;
    $hasCategory = isset($GLOBALS['CATEGORY']);
    echo '<ol>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      $product = DbProduct::get($id);
      $merchant = DbMerchant::get($product['merchant_id']);
      $title = self::highlight($product['title'], $keywordList);
      $specification = self::highlight(
        self::excerpt($product['property_list'], $keywordList), $keywordList
      );
      $href = 'http://www.360buy.com/product/'.$product['uri_argument_list'];
      $tagList = self::getTagList($product, $hasCategory);
      echo '<li>',
        '<div class="image"><a href="', $href, '" target="_blank" rel="nofollow">',
        '<img alt="', $title, '" src="http://img.dev.huobiwanjia.com/',
        $product['id'], '.jpg"/></a></div>',//image
        '<h3><a href="', $href, '" target="_blank" rel="nofollow">',
        $title, '</a></h3>',//title
        '<div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100, '</span></div>';//price
      if ($specification !== '') {
        echo '<p>', $specification, '&hellip;</p>';//specification
      }
      if (count($tagList) !== 0) {
        echo '<div class="tag_list">', implode(' ', $tagList), '</div>';
      }
      echo '<div class="merchant">', $merchant['name'], '</div>',//merchant
        '</li>';
    }
    echo '<ol>';
  }

  private static function getTagList($product, $hasCategory) {
    $result = array();
    if ($product['query_name'] !== null) {
      $result[] = '<a href="/+-'.urlencode($product['query_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">同款</a>';
    }
    if ($hasCategory === false && $product['category_name'] !== null) {
      $result[] = '<a href="'.urlencode($product['category_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">分类：'
        .$product['category_name'].'</a>';
      return $result;
    }
    if ($hasCategory === true && $product['brand_name'] !== null) {
      $result[] = '<a href="'.urlencode('品牌='.$product['brand_name'])
        .'/'.$GLOBALS['QUERY_STRING'].'" rel="nofollow">品牌：'
        .$product['brand_name'].'</a>';
    }
    return $result;
  }

  private static function excerpt($propertyList, $keywordList) { //TODO
    $list = array();
    foreach ($keywordList as $keyword) {
      //for ($i = 0; $i < 10000; $i++) {
        preg_match('{\n.*'.$keyword.'.*}', ','.$propertyList.',', $matches);
      //}
      //var_dump($matches);
    }
    //$propertyList = explode(';', $propertyList);
    return $propertyList;
  }

  private static function highlight($text, $keywordList) { //TODO
    $text = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
    foreach ($keywordList as $keyword) {
      if (strpos($text, ",$keyword,") !== false) {
        str_replace(",$keyword,", "<span>$keyword</span>", $text);
      }
    }
    $text = str_replace('+', '', $text);
    return str_replace('&amp;#43;', '+', $text);
  }
}