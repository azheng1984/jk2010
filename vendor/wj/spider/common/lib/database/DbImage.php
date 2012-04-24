<?php
class DbImage {
  private static $connectionList = array();

  public static function insertImage($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    Db::insert('image', array('product_id' => $productId, 'image' => $image));
    DbConnection::connect('default');
  }

  public static function updateImage($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    Db::update('image', array('image' => $image), 'product_id = ?', $productId);
    DbConnection::connect('default');
  }

  public static function deleteImage($tablePrefix, $productId) {
    self::connect($tablePrefix);
    Db::delete('image', 'product_id = ?', $productId);
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