<?php
class DbPropertyKey {
  public static function get($id) {
    $sql = 'SELECT * FROM property_key  WHERE id = ?';
    return Db::getRow($sql, $id);
  }

  public static function getByName($categoryId, $name) {
    $sql = 'SELECT * FROM property_key WHERE category_id= ? AND `name` = ?';
    return Db::getRow($sql, $categoryId, $name);
  }

  public static function getList(
    $categoryId, $alphabetIndex, $page, $amount = 60
  ) {
    $sql = 'SELECT * FROM property_key WHERE category_id = ?';
    if ($alphabetIndex !== null) {
      $sql .= " AND alphabet_index = $alphabetIndex";
    }
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `rank` LIMIT '.$start.','.$amount;
    return Db::getAll($sql, $categoryId);
  }
}