<?php
class ImageDb {
  public static function get($categoryId, $productId) {
    DbConnection::connect('image');
    $image = Db::getColumn($categoryId, array('product_id' => $productId));
    DbConnection::close();
    return $image;
  }

  public static function hasImage($categoryId, $productId) {
    DbConnection::connect('image');
    $id = Db::getColumn(
      'SELECT product_id FROM image WHERE product_id = ?', $productId
    );
    DbConnection::close();
    return $id !== false;
  }

  public static function insert($categoryId, $productId, $image) {
    DbConnection::connect('image');
    Db::insert(
      $categoryId, array('product_id' => $productId, 'image' => $image)
    );
    DbConnection::close();
  }

  public static function update($categoryId, $productId, $image) {
    DbConnection::connect('image');
    Db::update(
      $categoryId, array('image' => $image), 'product_id = ?', $productId
    );
    DbConnection::close();
  }

  public static function delete($categoryId, $productId) {
    DbConnection::connect('image');
    Db::delete($categoryId, 'product_id = ?', $productId);
    DbConnection::close();
  }

  public static function deleteTable($categoryId) {
    DbConnection::connect('image');
    Db::execute('DROP TABLE '.categoryId);
    DbConnection::close();
  }

  public static function createTable($categoryId) {
    DbConnection::connect('image');
    Db::execute(
      'CREATE TABLE `'.$categoryId.'` (
        `product_id` bigint(20) unsigned NOT NULL,
        `image` mediumblob NOT NULL,
        PRIMARY KEY (`product_id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1'
    );
    DbConnection::close();
  }
}