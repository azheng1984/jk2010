<?php
class ImageDb {
  private static $connectionList = array();

  public static function insert($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    Db::insert('image', array('product_id' => $productId, 'image' => $image));
    DbConnection::connect($GLOBALS['MERCHANT']);
  }

  public static function update($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    Db::update('image', array('image' => $image), 'product_id = ?', $productId);
    DbConnection::connect($GLOBALS['MERCHANT']);
  }

  public static function delete($tablePrefix, $productId) {
    self::connect($tablePrefix);
    Db::delete('image', 'product_id = ?', $productId);
    DbConnection::connect($GLOBALS['MERCHANT']);
  }

  public static function tryCreateDb($tablePrefix) {
    $path = IMAGE_PATH.$GLOBALS['MERCHANT'].'/'.$tablePrefix.'_image.sqlite';
    if (file_exists($path) === false) {
      DbConnection::connect($tablePrefix.'_image', new PDO('sqlite:'.$path));
      Db::execute('CREATE TABLE "image"'
        .'("product_id" INTEGER PRIMARY KEY NOT NULL, "image" BLOB NOT NULL)');
      DbConnection::connect($GLOBALS['MERCHANT']);
    }
  }

  private static function connect($merchant, $tablePrefix) {
    if (isset(self::$connectionList[$tablePrefix]) === false) {
      DbConnection::connect(
        $tablePrefix.'_image',
        new PDO('sqlite:'.IMAGE_PATH.$GLOBALS['MERCHANT'].'/'.$tablePrefix
          .'_image.sqlite')
      );
      self::$connectionList[$tablePrefix] = true;
      return;
    }
    DbConnection::connect($tablePrefix.'_image');
  }
}