<?php
class ProductPriceUpdater {
  public function execute($item) {
    $product = DbProduct::get($item['product_id']);
    $saleRank = 1000000 - $product['sale_index'];
    DbProduct::updateSearchSaleRank($product['web_product_id'], $saleRank);
  }
}