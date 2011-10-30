<?php
class DbProductUpdate {
  public static function insert($tablePrefix, $productId, $type) {
    Db::execute(
      'INSERT INTO '.$tablePrefix
      .'_product_update(product_id, type) VALUE(?, ?)',
      $productId, $type
    );
  }

  public static function createTable($tablePrefix) {
    if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product_update') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product_update` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `product_id` int(11) unsigned NOT NULL,
        `type` enum('PRICE','CONTENT','IMAGE') NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      Db::execute($sql);
    }
  }
}