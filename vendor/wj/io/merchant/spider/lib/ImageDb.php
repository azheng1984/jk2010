<?php
class ImageDb {
  private static $tableList = array();

  public static function get($categoryId, $productId) {
    DbConnection::connect('image');
    $image = Db::getColumn('c'.$categoryId, array('product_id' => $productId));
    DbConnection::close();
    return $image;
  }

  public static function hasImage($categoryId, $productId) {
    DbConnection::connect('image');
    $id = Db::getColumn(
      'SELECT product_id FROM c'.$categoryId.' WHERE product_id = ?', $productId
    );
    DbConnection::close();
    return $id !== false;
  }

  public static function insert($categoryId, $productId, $image) {
    DbConnection::connect('image');
    Db::insert(
      'c'.$categoryId, array('product_id' => $productId, 'image' => $image)
    );
    DbConnection::close();
  }

  public static function update($categoryId, $productId, $image) {
    DbConnection::connect('image');
    Db::update(
      'c'.$categoryId, array('image' => $image), 'product_id = ?', $productId
    );
    DbConnection::close();
  }

  public static function delete($categoryId, $productId) {
    DbConnection::connect('image');
    Db::delete('c'.$categoryId, 'product_id = ?', $productId);
    DbConnection::close();
  }

  public static function deleteTable($categoryId) {
    DbConnection::connect('image');
    Db::execute('DROP TABLE c'.categoryId);
    unset(self::$tableList[$categoryId]);
    DbConnection::close();
  }

  public static function createTable($categoryId) {
    DbConnection::connect('image');
    Db::execute(
      'CREATE TABLE `c'.$categoryId.'` (
        `product_id` bigint(20) unsigned NOT NULL,
        `image` mediumblob NOT NULL,
        PRIMARY KEY (`product_id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1'
    );
    DbConnection::close();
  }

  public static function tryCreateTable($categoryId) {
    if (isset(self::$tableList[$categoryId])) {
      return;
    }
    DbConnection::connect('image');
    $table = Db::getRow('SHOW TABLES LIKE "c'.$categoryId.'"');
    DbConnection::close();
    if ($table === false) {
      self::createTable($categoryId);
    }
    self::$tableList[$categoryId] = true;
  }
}