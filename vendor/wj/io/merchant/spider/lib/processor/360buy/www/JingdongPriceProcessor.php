<?php
class JingdongPriceProcessor {
  public function execute($path) {
    $result = WebClient::get(
      'jd2008.360buy.com',
      '/purchase/minicartservice.aspx?method=GetCart',
      array(),
      'yCartOrderLogic={&TheSkus&:[{&Id&:'.$path.'$&Num&:1}]};'
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
    if ((int)$priceX100 !== (int)$currentListPriceX100) {
      Db::update(
        '`product`',
        array(
          'price_from_x_100' => $currentPriceX100,
        ),
        'merchant_product_id = ?', $path
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