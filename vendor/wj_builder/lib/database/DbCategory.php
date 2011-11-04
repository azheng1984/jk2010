<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow(
      'SELECT * FROM `jingdong`.`category` WHERE id = ?', $id
    );
  }

  public static function getWeb($id) {
    return Db::getRow(
      'SELECT * FROM `wj_web`.`category` WHERE id = ?', $id
    );
  }

  public static function insertIntoWeb($id, $name) {
    Db::execute(
      'INSERT INTO `wj_web`.`category`(`id`, `name`) VALUES(?, ?)', $id, $name
    );
  }
}