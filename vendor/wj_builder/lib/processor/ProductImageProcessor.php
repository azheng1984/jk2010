<?php
class ProductImageProcessor {
  public function execute($item) {
    $spiderProductWebProduct =
      DbBuilderSpiderProductWebProduct::getBySpiderProductId(
        $item['product_id']
      );
    if ($spiderProductWebProduct === false) {
      return;
    }
    $webProductId = $spiderProductWebProduct['web_product_id'];
    $image = DbSpiderImage::get($item['product_id']);
    if ($image === false) {
      DbWebProduct::updateImageDbIndex(null);
      DbWebImage::delete($webProductId);
      return;
    }
    DbWebProduct::updateImageDbIndex(1);
    DbWebImage::replace($webProductId, $image);
  }
}