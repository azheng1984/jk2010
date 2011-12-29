<?php
class DbPropertyValue {
  public static function get($id) {
    $sql = 'SELECT * FROM property_value  WHERE id = ?';
    return Db::getRow($sql, $id);
  }

  public static function getByName($keyId, $name) {
    $sql = 'SELECT * FROM property_value WHERE key_id=? AND `name` = ?';
    return Db::getRow($sql, $keyId, $name);
  }

  public static function getList(
    $keyId, $alphabetIndex, $page, $amount = 60
  ) {
    $sql = 'SELECT * FROM property_key WHERE key_id = ?';
    if ($alphabetIndex !== null) {
      $sql .= " AND alphabet_index = $alphabetIndex";
    }
    $start = ($page - 1) * 60;
    $sql .= ' ORDER BY `rank` LIMIT '.$start.','.$amount;
    return Db::getAll($sql, $keyId);
  }
}