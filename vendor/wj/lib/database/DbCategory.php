<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM category WHERE id = ?', $id);
  }

  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM category WHERE `name` = ?', $name
    );
  }

  public static function getList($alphabetIndex, $page, $amount = 60) {
    $sql = 'SELECT * FROM category';
    if ($alphabetIndex !== null) {
      $sql .= " WHERE alphabet_index = $alphabetIndex";
    }
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `rank` LIMIT '.$start.','.$amount;
    return Db::getAll($sql);
  }

  public static function count($alphabetIndex) {
    $sql = 'SELECT count(*) FROM category';
    if ($alphabetIndex !== null) {
      $sql .= " WHERE alphabet_index = $alphabetIndex";
    }
    return Db::getColumn($sql);
  }
}