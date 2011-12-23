<?php
class DbProductProperty {
  public static function replace(
    $tablePrefix, $productId, $propertyValueId
  ) {
    Db::execute(
      'REPLACE INTO '.$tablePrefix.'_product-property SET'
      .' product_id = ?, property_value_id = ?, is_updated = 1',
      $productId, $propertyValueId
    );
  }

  public static function getListByProductId(
    $tablePrefix, $productId
  ) {
    Db::getAll('SELECT property_value_id FROM '.$tablePrefix.'_product-property'
      .' WHERE product_id = ? AND is_updated = TRUE', $productId);
  }

  public static function expireAll($tablePrefix) {
    Db::execute(
      'UPDATE '.$tablePrefix.'_product-property SET is_updated = 0'
    );
  }

  public static function deleteExpiredItems($tablePrefix) {
    Db::execute(
      'DELETE '.$tablePrefix.'_product-property WHERE is_updated = 0'
    );
  }

  public static function createTable($tablePrefix) {
    $table = Db::getColumn(
      'SHOW TABLES LIKE ?', $tablePrefix.'_product-property'
    );
    if ($table === false) {
      $sql = "CREATE TABLE `".$tablePrefix."_product-property` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `merchant_product_id` bigint(20) unsigned NOT NULL,
        `property_value_id` int(11) unsigned NOT NULL,
        `is_update` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `merchant_product_id-property_value_id`
          (`merchant_product_id`,`property_value_id`),
        KEY `merchant_product_id-is_update` (`merchant_product_id`,`is_update`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      Db::execute($sql);
    }
  }
}