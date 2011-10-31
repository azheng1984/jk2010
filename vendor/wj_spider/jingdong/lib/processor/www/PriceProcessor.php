<?php
class PriceProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      array(),
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$arguments['id'].'$&Num&:1}]};'
    );
    if ($result['content'] === false) {
      return $result;
    }
    $tablePrefix = $arguments['table_prefix'];
    $merchantProductId = $arguments['id'];
    $matches = array();
    preg_match(
      '{"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),}',
      $result['content'],
      $matches
    );
    if (count($matches) !== 4) {
      return;
    }
    $price = $matches[3];
    $row = DbProduct::getPrice($tablePrefix, $merchantProductId);
    if ($row['lowest_price'] !== $price) {
      DbProduct::updatePrice($tablePrefix, $row['id'], $price);
      DbProductUpdate::insert($tablePrefix, $row['id'], 'PRICE');
    }
  }
}