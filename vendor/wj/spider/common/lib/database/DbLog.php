<?php
class DbLog {
  public static function insert($tablePrefix, $productId, $type) {
    Db::execute(
      'INSERT INTO '.$tablePrefix
      .'_product_log(product_id, type) VALUE(?, ?)',
      $productId, $type
    );
  }

  public static function createTable($tablePrefix) {
    if (
      Db::getColumn('SHOW TABLES LIKE ?', $tablePrefix.'_product_log') === false
    ) {
      $sql = "CREATE TABLE `".$tablePrefix."_product_log` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `product_id` int(11) unsigned NOT NULL,
        `type` enum('PRICE','CONTENT','IMAGE','SALE_RANK') NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
      Db::execute($sql);
    }
  }
}