<?php
class PriceProcessor {
  public function execute($arguments) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      array(),
      'yCartOrderLogic={&TheSkus&:[{&Id&:'
        .$arguments['merchant_product_id'].'$&Num&:1}]};'
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
      return;
    }
    $tablePrefix = $arguments['table_prefix'];
    $listPrice = $matches[1];
    $price = $matches[3];
    $row = DbProduct::getPrice($tablePrefix, $arguments['id']);
    if ($row['lowest_price'] !== $price
      || $row['lowest_list_price'] !== $listPrice) {
      $this->updatePrice(
        $tablePrefix,
        $row['id'],
        $price,
        $listPrice,
        $arguments['is_content_updated']
      );
    }
  }

  private function updatePrice(
    $tablePrefix, $productId, $price, $listPrice, $isContentUpdated
  ) {
    DbProduct::updatePrice(
      $tablePrefix, $productId, $price, null, $listPrice
    );
    if (!$isContentUpdated) {
      DbProductLog::insert($tablePrefix, $productId, 'PRICE');
    }
  }
}