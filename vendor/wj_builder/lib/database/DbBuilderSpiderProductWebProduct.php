<?php
class DbBuilderSpiderProductWebProduct {
  public function get($merchantProductId) {
    return Db::getRow('SELECT * FROM spider_product-web_product');
  }

  public function insert($merchantId, $merchantProductId, $webProductId) {
    $sql = 'INSERT INTO spider_product-web_product('
      .'`merchant_id`,`merchant_product_id`,`web_product_id`) VALUES(?, ?, ?)';
    Db::execute($sql, $merchantId, $merchantProductId, $webProductId);
  }
}