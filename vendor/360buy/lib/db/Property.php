<?php
class Property extends Db {
  public function getOrNewKeyId($categoryId, $name) {
    $sql = "select * from property_key"
      ." where category_id=$categoryId and `key` = '$name'";
    $row = $this->getRow($sql);
    if ($row === false) {
      $this->executeNonQuery("insert into property_key(`key`, category_id)"
        ." values('$name', $categoryId)");
      return $this->getLastInsertId();
    }
    return $row['id'];
  }

  public function getOrNewValueId($keyId, $name) {
    $sql = "select * from property_value"
      ." where key_id=$keyId and `value` = '$name'";
    $row = $this->getRow($sql);
    if ($row === false) {
      $sql = "insert into property_value(`value`, key_id)"
        ." values('$name', $keyId)";
      $this->executeNonQuery($sql);
      return $this->getLastInsertId();
    }
    return $row['id'];
  }
}