<?php
class DbQuery {
  public function get($id) {
    return Db::getRow('SELECT * FROM query WHERE id = ?', $id);
  }

  public function getByName($name) {
    return Db::getRow('SELECT * FROM query WHERE name = ?', $name);
  }

  public static function getList($categoryId, $page) {
    $sql = 'SELECT * FROM query WHERE category_id = ?';
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `popularity_rank` LIMIT '.$start.',100';
    return Db::getAll($sql, $categoryId);
  }
}