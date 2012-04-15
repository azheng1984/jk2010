<?php
class DbSpiderCategory {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`category` WHERE id = ?', $id
    );
  }
}