<?php
class DbCategory {
  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM category WHERE `name` = ?', $name
    );
  }

  public static function getList($page) {
    $start = ($page - 1) * 100;
    $sql = 'SELECT * FROM category ORDER BY `product_amount` LIMIT '
      .$start.',100';
    return Db::getAll($sql);
  }
}