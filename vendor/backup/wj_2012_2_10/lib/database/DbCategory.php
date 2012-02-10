<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM category WHERE id = ?', $id);
  }

  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM category WHERE `name` = ?', $name
    );
  }

  public static function getList($page) {
    $sql = 'SELECT * FROM category';
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `product_amount` LIMIT '.$start.',100';
    return Db::getAll($sql);
  }
}