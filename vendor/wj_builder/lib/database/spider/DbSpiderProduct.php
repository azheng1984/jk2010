<?php
class DbSpiderProduct {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_product` WHERE id = ?', $id
    );
  }

  public static function getPropertyValueList($id) {
    return Db::getAll(
      'SELECT * FROM `jingdong`.`food_product_property`'
      .' WHERE product_id = ?',
      $id
    );
  }
}