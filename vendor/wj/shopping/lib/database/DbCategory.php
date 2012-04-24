<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM category WHERE id = ?', $id);
  }

  public static function getByName($name) {
    return Db::getRow('SELECT * FROM category WHERE name = ?', $name);
  }

  public static function getList($page, $orderBy = 'product_amount', $itemsPerPage = 100) {
    $offset = ($page - 1) * $itemsPerPage;
    return Db::getAll('SELECT * FROM category ORDER BY product_amount LIMIT '
      .$offset.', '.$itemsPerPage);
  }
}