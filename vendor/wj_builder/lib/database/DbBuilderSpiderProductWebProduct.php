<?php
class DbBuilderSpiderProductWebProduct {
  public function get($merchantProductId) {
    $sql = 'SELECT * FROM spider_product-web_product'
      .' WHERE merchant_product_id = ?';
    return Db::getRow($sql, $merchantProductId);
  }

  public function getBySpiderProductId($spiderProductId) {
    $sql = 'SELECT * FROM spider_product-web_product'
      .' WHERE spider_product_id = ?';
    return Db::getRow($sql, $spiderProductId);
  }

  public function insert(
    $spiderProductId, $merchantId, $merchantProductId, $webProductId
  ) {
    $sql = 'INSERT INTO spider_product-web_product(`spider_product_id`,'
      .'`merchant_id`,`merchant_product_id`,`web_product_id`)'
      .' VALUES(?, ?, ?, ?)';
    Db::execute(
      $sql, $spiderProductId, $merchantId, $merchantProductId, $webProductId
    );
  }
}