<?php
class DbProperty {
  public static function getByValueId($tablePrefix, $valueId) {
    $keyTable = $tablePrefix.'_property_key';
    $valueTable = $tablePrefix.'_property_value';
    $sql = 'SELECT * FROM '.$valueTable.' table_value LEFT JOIN '
      .$keyTable.' table_key ON table_key.id = table_value.key_id'
      .' WHERE table_value.id = ?';
    return Db::getRow($sql, $valueId);
  }
}