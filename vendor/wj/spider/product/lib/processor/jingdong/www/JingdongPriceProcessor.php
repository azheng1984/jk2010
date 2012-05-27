<?php
class JingdongPriceProcessor {
  public function execute(
    $tablePrefix, $categoryId, $productId,
    $merchantProductId, $priceX100, $listPriceX100
  ) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      array(),
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$merchantProductId.'$&Num&:1}]};'
    );
    preg_match(
      '{"ListPrice":(.*?),"Price":(.*?),.*?"PromotionPrice":(.*?),}',
      $result['content'],
      $matches
    );
    if (count($matches) !== 4) {
      return;
    }
    $currentListPriceX100 = $matches[1] * 100;
    if ($currentListPriceX100 === 0) {
      $currentListPriceX100 = null;
    }
    $currentPriceX100 = $matches[3] * 100;
    if ($currentPriceX100 === 0) {
      $currentPriceX100 = null;
    }
    if ((int)$priceX100 !== (int)$currentListPriceX100
      || (int)$listPriceX100 !== (int)$currentPriceX100) {
      Db::update(
        '`'.$tablePrefix.'-product`',
        array(
          'price_from_x_100' => $currentPriceX100,
          'list_price_x_100' => $currentListPriceX100
        ),
        'id = ?', $productId
      );
      $this->log($tablePrefix, $categoryId, $productId, $priceX100);
    }
  }

  private function log($tablePrefix, $categoryId, $productId, $priceX100) {
    if ($priceX100 !== null) {
      Db::insert(
        '`'.$tablePrefix.'-log`',
        array(
          'type' => 'PRICE',
          'product_id' => $productId,
          'category_id' => $categoryId
        )
      );
    }
  }
}