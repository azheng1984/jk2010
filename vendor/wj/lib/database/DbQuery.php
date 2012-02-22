<?php
class DbQuery {
  public static function getList($categoryId, $page) {
    $offset = ($page - 1) * 100;
    $sql = 'SELECT * FROM query WHERE category_id = ? ORDER BY'
      .' `popularity_rank` LIMIT '.$offset.',100';
    return Db::getAll($sql, $categoryId);
  }

  public function getByName($name) {
    return Db::getRow('SELECT * FROM query WHERE name = ?', $name);
  }
}