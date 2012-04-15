<?php
class DbSpiderKey {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_property_key` WHERE id = ?', $id
    );
  }
}