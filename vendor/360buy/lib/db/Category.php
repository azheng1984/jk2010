<?php
class Category extends Db {
  public function getOrNewId($name, $parentId = null) {
    $connection = new DbConnection;
    $sql = "select * from category where `name` = '$name' and "
      .$this->getFilter('parent_id', $parentId);
    $row = $connection->getRow($sql);
    if ($row === false) {
      $connection->executeNonQuery("insert into category(`name`, parent_id)"
        ." values('$name', ?)", array($parentId));
      return $connection->getLastInsertId();
    }
    return $row['id'];
  }
}