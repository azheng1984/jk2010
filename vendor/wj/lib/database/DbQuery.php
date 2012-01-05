<?php
class DbQuery {
  public function get($id) {
    return Db::getRow('SELECT * FROM query WHERE id = ?', $id);
  }

  public static function getList($categoryId, $page) {
    $sql = 'SELECT * FROM query WHERE category_id = ?';
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `product_amount` LIMIT '.$start.',100';
    return Db::getAll($sql, $categoryId);
  }
}