<?php
class Category {
  public static function getList($parentId = null) {
    $sql = 'select * from global_category where parent_id';
    if ($parentId === null) {
      $sql .= ' is null';
    } else {
      $sql .= '='.$parentId;
    }
    return Db::execute($sql);
  }

  public function getParent() {
  }
}