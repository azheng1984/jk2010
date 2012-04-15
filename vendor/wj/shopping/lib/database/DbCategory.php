<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM category WHERE `id` = ?', $id);
  }

  public static function getByName($name) {
    return Db::getRow('SELECT * FROM category WHERE `name` = ?', $name);
  }

  public static function getList($page) {
    $offset = ($page - 1) * 100;
    $sql = 'SELECT * FROM category ORDER BY `product_amount` LIMIT '
      .$offset.',100';
    return Db::getAll($sql);
  }
}