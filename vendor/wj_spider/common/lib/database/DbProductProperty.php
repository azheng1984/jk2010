<?php
class DbProductProperty {
  public static function replace(
    $tablePrefix, $merchantProductId, $propertyId
  ) {
    Db::execute(
      'REPLACE INTO '.$tablePrefix.'_product_property SET'
      .' merchant_product_id = ?, property_value_id = ?',
      $merchantProductId, $propertyId
    );
  }

  public static function deleteAll($tablePrefix) {
    Db::execute('DELETE FROM '.$tablePrefix.'_product_property');
  }

  public static function createTable($tablePrefix) {
    
  }
}