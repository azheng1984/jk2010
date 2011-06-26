<?php
class Category extends Db {
  public function getOrNewId($name, $parentId = null) {
    $sql = "select * from category where `name` = '$name' and "
      .$this->getFilter('parent_id', $parentId);
    $row = $this->getRow($sql);
    if ($row === false) {
      $this->executeNonQuery("insert into category(`name`, parent_id)"
        ." values('$name', ?)", array($parentId));
      return $this->getLastInsertId();
    }
    return $row['id'];
  }
}