<?php
class ProductPriceProcessor {
  public function execute($item) {
    $spiderProductWebProduct =
      DbBuilderSpiderProductWebProduct::getBySpiderProductId(
        $item['product_id']
      );
    if ($spiderProductWebProduct === false) {
      return;
    }
    $spiderProductId = $spiderProductWebProduct['spider_product_id'];
    $priceList = DbSpiderProduct::getPriceList($spiderProductId);
    $discountX10 = 10;
    $webProductId = $spiderProductWebProduct['web_product_id'];
    DbSearchProduct::updatePrice(
      $webProductId, $priceList['lowest_price_x_100'], $discountX10
    );
    DbWebProduct::updatePrice(
      $webProductId,
      $priceList['lowest_price_x_100'],
      $priceList['highest_price_x_100'],
      $priceList['list_lowest_price_x_100']
    );
  }
}