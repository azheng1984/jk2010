<?php
class DbQuery {
  public function get($id) {
    return Db::getRow('SELECT * FROM query WHERE id = ?', $id);
  }

  public static function getList(
    $categoryId, $alphabetIndex, $page, $amount = 60
  ) {
    $sql = 'SELECT * FROM query WHERE category_id = ?';
    if ($alphabetIndex !== null) {
      $sql .= " AND alphabet_index = $alphabetIndex";
    }
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `amount` LIMIT '.$start.','.$amount;
    return Db::getAll($sql, $categoryId);
  }
}