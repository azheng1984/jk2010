<?php
class DbCategory {
  public static function get($id) {
    return Db::getRow('SELECT * FROM wj_web.category WHERE id = ?', $id);
  }

  public static function getList($alphabetIndex, $page) {
    $sql = 'SELECT * FROM wj_web.category';
    if ($alphabetIndex !== null) {
      $sql .= " WHERE alphabet_index = $alphabetIndex";
    }
    $start = ($page - 1) * 60;
    $sql .= ' LIMIT '.$start.',60';
    return Db::getAll($sql);
  }

  public static function count($alphabetIndex) {
    $sql = 'SELECT count(*) FROM wj_web.category';
    if ($alphabetIndex !== null) {
      $sql .= " WHERE alphabet_index = $alphabetIndex";
    }
    return Db::getColumn($sql);
  }

  public static function getByName($name) {
    return Db::getRow(
      'SELECT * FROM wj_web.category WHERE `name` = ?', $name
    );
  }
}