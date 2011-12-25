<?php
class DbBuilderSpiderProductWebProduct {
  public static function get($merchantProductId) {
    $sql = 'SELECT * FROM `wj_builder`.`spider_product-web_product`'
      .' WHERE merchant_product_id = ?';
    return Db::getRow($sql, $merchantProductId);
  }

  public static function getBySpiderProductId($spiderProductId) {
    $sql = 'SELECT * FROM `wj_builder`.`spider_product-web_product`'
      .' WHERE spider_product_id = ?';
    return Db::getRow($sql, $spiderProductId);
  }

  public static function insert(
    $spiderProductId, $merchantId, $merchantProductId, $webProductId
  ) {
    $sql = 'INSERT INTO `wj_builder`.`spider_product-web_product`(`spider_product_id`,'
      .'`merchant_id`,`merchant_product_id`,`web_product_id`)'
      .' VALUES(?, ?, ?, ?)';
    Db::execute(
      $sql, $spiderProductId, $merchantId, $merchantProductId, $webProductId
    );
  }
}