<?php
class ProductSaleRankProcessor {
  public function execute($item) {
    $spiderProductWebProduct =
      DbBuilderSpiderProductWebProduct::getBySpiderProductId(
        $item['product_id']
      );
    if ($spiderProductWebProduct === false) {
      return;
    }
    $spiderProductId = $spiderProductWebProduct['spider_product_id'];
    $saleRank = DbSpiderProduct::getSaleRank($spiderProductId);
    $webProductId = $spiderProductWebProduct['web_product_id'];
    DbSearchProduct::updateSaleRank($webProductId, 10000 - $saleRank);
  }
}