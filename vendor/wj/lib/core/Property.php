<?php
class Property {
  public static function retrieve($uniqueName) {
    $sql = "select * from global_property_key where name='$uniqueName'";
    return Db::execute($sql);
  }
}