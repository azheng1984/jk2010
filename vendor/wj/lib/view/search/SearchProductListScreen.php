<?php
class SearchProductListScreen {
  public static function render($searchScreen) {
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
      echo '<h3>', $product['title'], '</h3>';
      echo '<div class="merchant">', $merchant['name'], '</div>';
      echo '</li>';
      $metaList[] = self::getMeta($product, $hasCategory);
    }
    echo '<ol>';
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
}