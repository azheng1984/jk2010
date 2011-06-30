<?php
class PriceProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$arguments['id'].'$&Num&:1}]};'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $matches = array();
    preg_match(
      '{"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),}',
      $result['content'],
      $matches
    );
    if (count($matches) !== 4) {
      return $result;
    }
    DbProduct::updatePrice(
      $arguments['id'], $matches[1], $matches[2], $matches[3]
    );
  }
}