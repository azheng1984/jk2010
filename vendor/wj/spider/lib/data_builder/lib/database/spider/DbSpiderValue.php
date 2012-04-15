<?php
class DbSpiderValue {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`food_property_value` WHERE id = ?', $id
    );
  }
}