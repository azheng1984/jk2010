<?php
class JingdongPriceProcessor {
  public function execute(
    $tablePrefix, $productId, $merchantProductId, $priceX100, $listPriceX100
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
    $currentPriceX100 = $matches[3] * 100;
    if ((int)$priceX100 !== $currentListPriceX100
      || (int)$listPriceX100 !== $currentPriceX100) {
      Db::update(
        $tablePrefix.'-product',
        array(
          'price_x_100' => $currentPriceX100,
          'list_price_x_100' => $currentListPriceX100
        ),
        'id = ?', $productId);
      Db::insert(
        $tablePrefix,'-log',
        array('type' => 'PRICE', 'product_id' => $productId)
      );
    }
  }
}