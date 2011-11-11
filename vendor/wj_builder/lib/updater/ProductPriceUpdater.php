<?php
class ProductPriceUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['product_id']);
    $lowestPriceX100 = null;
    if ($product['lowest_price'] !== null) {
      $lowestPriceX100 = $product['lowest_price'] * 100;
    }
    $highestPriceX100 = null;
    if ($product['highest_price'] !== null) {
      $highestPriceX100 = $product['highest_price'] * 100;
    }
    $discountX10 = 100;
    $isUpdate = true;//TODO
    if ($isUpdate) {
      DbProduct::updateWebPrice(
        $product['web_product_id'], $lowestPriceX100, $highestPriceX100
      );
      DbProduct::updateSearchPrice(
        $product['web_product_id'], $lowestPriceX100, $discountX10
      );
    }
  }
}