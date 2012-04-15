<?php
class DbProductProperty {
  public static function replace(
    $tablePrefix, $merchantProductId, $propertyValueId
  ) {
    Db::execute(
      'REPLACE INTO `'.$tablePrefix.'_product-property` SET'
      .' merchant_product_id = ?, property_value_id = ?, is_updated = 1',
      $merchantProductId, $propertyValueId
    );
  }

  public static function getListByMerchantProductId(
    $tablePrefix, $merchantProductId
  ) {
    return Db::getAll(
      'SELECT id FROM `'
        .$tablePrefix.'_product-property` WHERE merchant_product_id = ?',
      $merchantProductId
    );
  }

  public static function expireAll($tablePrefix) {
    Db::execute(
      'UPDATE `'.$tablePrefix.'_product-property` SET is_updated = 0'
    );
  }

  public static function deleteExpiredItems($tablePrefix) {
    Db::execute(
      'DELETE `'.$tablePrefix.'_product-property` WHERE is_updated = 0'
    );
  }

  public static function createTable($tablePrefix) {
    $table = Db::getColumn(
      'SHOW TABLES LIKE ?', $tablePrefix.'_product-property'
    );
    if ($table === false) {
      $sql = "CREATE TABLE ".$tablePrefix."_product-property (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `merchant_product_id` bigint(20) unsigned NOT NULL,
        `property_value_id` int(11) unsigned NOT NULL,
        `is_updated` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `merchant_product_id` (`merchant_product_id`,`property_value_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      Db::execute($sql);
    }
  }
}