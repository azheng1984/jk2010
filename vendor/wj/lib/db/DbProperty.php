<?php
class DbProperty {
  public static function getListByProductId($productId) {
    $table = $GLOBALS['db_product_table_prefix'].'_property_key';
    $sql = 'SELECT * FROM '.$table.' WHERE id = ';
  }
}