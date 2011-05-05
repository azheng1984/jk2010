<?php
class Schema {
  public static function retrieve($uniqueName) {
    $sql = "select * from global_schema where unique_name='$uniqueName'";
    return Db::execute($sql);
  }
}