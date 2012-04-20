<?php
class DbSpiderProduct {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_product` WHERE id = ?', $id
    );
  }

  public static function getPropertyValueList($merchantProductId) {
    return Db::getAll(
      'SELECT * FROM `jingdong`.`food_product-property`'
      .' WHERE merchant_product_id = ?',
      $merchantProductId
    );
  }

  public static function getPriceList($id) {
    return Db::getRow(
      'SELECT `lowest_price_x_100`,`highest_price_x_100`,`lowest_list_price_x_100`'
        .' FROM `jingdong`.`food_product` WHERE id = ?',
      $id
    );
  }

  public static function getSaleRank($id) {
    return Db::getColumn(
      'SELECT sale_rank FROM `jingdong`.`food_product` WHERE id = ?', $id
    );
  }
}