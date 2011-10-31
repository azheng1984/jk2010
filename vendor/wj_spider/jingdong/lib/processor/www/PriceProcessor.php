<?php
class PriceProcessor {
  public function execute($arguments) {
    $arguments['id'] = '123';
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
    print_r($matches);
    exit;
    if (count($matches) !== 4) {
      return;//TODO:offline
    }
    $price = $matches[3];
    $row = DbProduct::getPrice($tablePrefix, $merchantProductId);
    if ($row['lowest_price'] !== $price) {
      DbProduct::updatePrice($tablePrefix, $row['id'], $price);
      DbProductUpdate::insert($tablePrefix, $row['id'], 'PRICE');
    }
  }
}