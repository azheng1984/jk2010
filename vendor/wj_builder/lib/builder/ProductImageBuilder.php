<?php
class ProductImageBuilder {
  public function execute($item) {
    $spiderProductWebProduct =
      DbBuilderSpiderProductWebProduct::getBySpiderProductId(
        $item['product_id']
      );
    if ($spiderProductWebProduct === false) {
      
    }
  }
}