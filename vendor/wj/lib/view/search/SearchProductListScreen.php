<?php
class SearchProductListScreen {
  public static function render($searchScreen) {
    return;
    $metaList = array();
    $index = 0;
    $hasCategory = isset($GLOBALS['CATEGORY']);
    echo '<ol>';
    foreach ($GLOBALS['SEARCH_RESULT']['matches'] as $id => $result) {
      $product = DbProduct::get($id);
      $merchant = DbMerchant::get($product['merchant_id']);
      echo '<li>';
      echo '<div class="image"><a href="" target="_blank" rel="nofollow">',
        '<img alt="'.$product['title'].'" src="http://img.dev.huobiwanjia.com/',
        $product['id'].'.jpg"/></a></div>';
      echo '<h3><a href="" target="_blank" rel="nofollow">',
        $product['title'], '</a></h3>';
      echo '<div class="price">&yen;<span>',
        $product['lowest_price_x_100']/100,'</span></div>';
      echo '<p>', $product['description'], '&hellip;</p>';
      echo '<div class="merchant">', $merchant['name'], '</div>';
      echo '</li>';
      $metaList[] = self::getMeta($product, $hasCategory);
    }
    echo '<ol>';
    self::renderMetaList($metaList);
  }

  private static function getMeta($product, $hasCategory) {
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

  private static function excerpt($text, $keywordList) { //TODO
  }

  private static function highlight($text, $keywordList) { //TODO
  }

  private static function renderMetaList() { //TODO
     
  }
}