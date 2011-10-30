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

  public static function getListByMerchantProductId(
    $tablePrefix, $merchantProductId
  ) {
    Db::getAll('SELECT property_value_id FROM '.$tablePrefix.'_product_property'
      .' WHERE merchant_product_id = ?', $merchantProductId);
  }

  public static function deleteAll($tablePrefix) {
    Db::execute('DELETE FROM '.$tablePrefix.'_product_property');
  }

  public static function createTable($tablePrefix) {
    $table = Db::getColumn(
      'SHOW TABLES LIKE ?', $tablePrefix.'_product_property'
    );
    if ($table === false) {
      $sql = "CREATE TABLE `".$tablePrefix."_product_property` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `merchant_product_id` int(11) unsigned NOT NULL,
        `property_value_id` int(11) unsigned NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `merchant_product_id-property_value_id`
          (`merchant_product_id`,`property_value_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      Db::execute($sql);
    }
  }
}