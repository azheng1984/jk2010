<?php
class DbImage {
  private static $connectionList = array();

  public static function insertImage($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    $sql = 'INSERT INTO image(product_id, image) VALUES(?, ?)';
    Db::execute($sql, $productId, $image);
    DbConnection::connect('default');
  }

  public static function updateImage($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    $sql = 'UPDATE image SET image = ? WHERE product_id = ?';
    Db::execute($sql, $image, $productId);
    DbConnection::connect('default');
  }

  public static function deleteImage($tablePrefix, $productId) {
    self::connect($tablePrefix);
    $sql = 'DELETE FROM image WHERE product_id = ?';
    Db::execute($sql, $productId);
    DbConnection::connect('default');
  }

  public static function tryCreateTable($tablePrefix) {
    if (file_exists(IMAGE_PATH.$tablePrefix.'_image.sqlite') === false) {
      DbConnection::connect(
        $tablePrefix.'_image',
        new PDO('sqlite:'.IMAGE_PATH.$tablePrefix.'_image.sqlite')
      );
      $sql = 'CREATE TABLE "image"'
        .' ("product_id" INTEGER PRIMARY KEY NOT NULL, "image" BLOB NOT NULL)';
      Db::execute($sql);
      DbConnection::connect('default');
    }
  }

  private static function connect($tablePrefix) {
    if (isset(self::$connectionList[$tablePrefix]) === false) {
      DbConnection::connect(
        $tablePrefix.'_image',
        new PDO('sqlite:'.IMAGE_PATH.$tablePrefix.'_image.sqlite')
      );
      self::$connectionList[$tablePrefix] = true;
      return;
    }
    DbConnection::connect($tablePrefix.'_image');
  }
}