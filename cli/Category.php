<?php
class Category {
  public function getOrNewId($name, $parentId = null) {
    $database = new Database;
    $sql = "select * from category where `name` = '$name' and "
      .$this->getFilter('parent_id', $parentId);
    $row = $database->getRow($sql);
    if ($row === false) {
      $database->executeNonQuery("insert into category(`name`) values('$name')");
      return $database->getLastInsertId();
    }
    return $row['id'];
  }

  private function getFilter($key, $value, $isNumber = true) {
    if ($value === null) {
      return $sql .= "`$key` is null";
    }
    if ($isNumber) {
      return $sql .= "`$key`=$value";
    }
    return $sql .= "`$key`='$value'";
  }
}