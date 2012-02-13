<?php
class DbQuery {
  public static function getList($categoryId, $page) {
    $start = ($page - 1) * 100;
    $sql = 'SELECT * FROM query WHERE category_id = ? ORDER BY'
      .' `popularity_rank` LIMIT '.$start.',100';
    return Db::getAll($sql, $categoryId);
  }
}