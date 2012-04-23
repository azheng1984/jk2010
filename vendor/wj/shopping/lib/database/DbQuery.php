<?php
class DbQuery {
  public static function getList($categoryId, $page, $itemsPerPage = 100) {
    $offset = ($page - 1) * $itemsPerPage;
    return Db::getAll('SELECT * FROM query WHERE category_id = ? ORDER BY'
      .' popularity_rank LIMIT '.$offset.', '.$itemsPerPage, $categoryId);
  }

  public function getByName($name) {
    return Db::getRow('SELECT * FROM query WHERE name = ?', $name);
  }
}