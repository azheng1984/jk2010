<?php
class PriceProcessor {
  public function execute($arguments) {
    $client = new WebClient;
    $result = $client->get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$arguments['id'].'$&Num&:1}]};'
    );
    $product = new Product;
    $matches = array();
    preg_match(
      '"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),',
      $result['content'],
      $matches
    );
    $product->updatePrice(
      $arguments['id'], $matches[0][1], $matches[0][1], $matches[0][1]
    );
  }
}