<?php
class DbCategory {
  public static function getOrNewId($name, $parentId = null) {
    $sql = "select * from category where `name` = '$name' and "
      .Db::getFilter('parent_id', $parentId);
    $row = Db::getRow($sql);
    if ($row === false) {
      Db::executeNonQuery("insert into category(`name`, parent_id)"
        ." values('$name', ?)", array($parentId));
      return Db::getLastInsertId();
    }
    return $row['id'];
  }
}