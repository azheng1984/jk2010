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
    $sql = 'SELECT lowest_price_x_100, lowest_list_price_x_100 FROM '
      .$tablePrefix.'_product WHERE id = ?';
    $row = Db::getColumn($sql, $arguments['id']);
    if ((int)$row['lowest_price_x_100'] !== $listPriceX100
      || (int)$row['lowest_list_price_x_100'] !== $priceX100) {
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
    Db::update(
      $tablePrefix.'_product',
      array('price_x_100' => $priceX100, 'list_proce_x_100' => $listPriceX100),
      'id = ?', $productId);
    if (!$isContentUpdated) {
      Db::insert(
        $tablePrefix,'_log',
        array('type' => 'PRICE', 'product_id' => $productId)
      );
    }
  }
}