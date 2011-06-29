<?php
class PriceProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$arguments['id'].'$&Num&:1}]};'
    );
    $matches = array();
    preg_match(
      '{"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),}',
      $result['content'],
      $matches
    );
    DbProduct::updatePrice(
      $arguments['id'], $matches[1], $matches[2], $matches[3]
    );
  }
}