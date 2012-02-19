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
      echo '<li>',
        '<div class="image"><a href="" target="_blank" rel="nofollow">',
        '<img alt="', $title, '" src="http://img.dev.huobiwanjia.com/',
        $product['id'], '.jpg"/></a></div>',//image
        '<h3><a href="" target="_blank" rel="nofollow">',
        $title, '</a></h3>',//title
        '<div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100, '</span></div>',//price
        '<p>', $specification, '&hellip;</p>',//specification
        '<div class="merchant">', $merchant['name'], '</div>';//merchant
      self::renderFooter($product, $hasCategory);
      echo '</li>';
    }
    echo '<ol>';
  }

  private static function renderFooter($product, $hasCategory) {
    $meta = array();
    if ($product['query_name'] !== null) {
      $meta[0] = $product['query_name'];
    }
    if ($hasCategory === false && $product['category_name'] !== null) {
      $meta[1] = $product['category_name'];
      return $meta;
    }
    if ($hasCategory === true && $product['brand_name'] !== null) {
      $meta[2] = $product['brand_name'];
    }
    return $meta;
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