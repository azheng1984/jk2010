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
    preg_match(
      '{"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),}',
      $result['content'],
      $matches
    );
    if (count($matches) !== 4) {
      return;
    }
    $tablePrefix = $arguments['table_prefix'];
    $listPriceX100 = $matches[1] * 100;
    $priceX100 = $matches[3] * 100;
    $row = DbProduct::getPrice($tablePrefix, $arguments['id']);
    if ($row['lowest_price_x_100'] != $listPriceX100
      || $row['lowest_list_price_x_100'] != $priceX100) {
      $this->updatePrice(
        $tablePrefix,
        $row['id'],
        $priceX100,
        $listPriceX100,
        $arguments['is_content_updated']
      );
    }
  }

  private function updatePrice(
    $tablePrefix, $productId, $priceX100, $listPriceX100, $isContentUpdated
  ) {
    DbProduct::updatePrice(
      $tablePrefix, $productId, $priceX100, null, $listPriceX100
    );
    if (!$isContentUpdated) {
      DbLog::insert($tablePrefix, $productId, 'PRICE');
    }
  }
}