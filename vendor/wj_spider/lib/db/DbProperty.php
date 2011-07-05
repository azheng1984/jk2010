<?php
class DbProperty {
  public static function getOrNewKeyId($categoryId, $name) {
    $sql = "select * from property_key"
      ." where category_id=$categoryId and `key` = '$name'";
    $row = Db::getRow($sql);
    if ($row === false) {
      Db::executeNonQuery("insert into property_key(`key`, category_id)"
        ." values('$name', $categoryId)");
      return Db::getLastInsertId();
    }
    return $row['id'];
  }

  public static function getOrNewValueId($keyId, $name) {
    $sql = "select * from property_value"
      ." where key_id=$keyId and `value` = '$name'";
    $row = Db::getRow($sql);
    if ($row === false) {
      $sql = "insert into property_value(key_id, `value`)"
        ." values($keyId, '$name')";
      Db::executeNonQuery($sql);
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}