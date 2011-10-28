<?php
class DbImage {
  private static $connectionList = array();

  public static function createTable($tablePrefix) {
    if (!file_exists($tablePrefix.'_image.sqlite')) {
      DbConnection::connect(
        $tablePrefix.'_image', new PDO('sqlite:'.$tablePrefix.'_image.sqlite')
      );
      $sql = 'CREATE  TABLE "main"."image" '
        .'("product_id" INTEGER PRIMARY KEY  NOT NULL , "image" BLOB NOT NULL)';
      Db::execute($sql);
      DbConnection::connect('defalut');
    }
  }

  public static function deleteImage($tablePrefix, $productId) {
    self::connect($tablePrefix);
    $sql = 'DELETE FROM image WHERE product_id = ?';
    Db::execute($sql, $productId);
    DbConnection::connect('default');
  }

  public static function replaceImage($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    $sql = 'REPLACE INTO image SET product_id = ?, image = ?';
    Db::execute($sql, $productId, $image);
    DbConnection::connect('default');
  }

  private static function connect($tablePrefix) {
    if (!isset(self::$connectionList[$tablePrefix])) {
      DbConnection::connect(
          $tablePrefix.'_image', new PDO('sqlite:'.$tablePrefix.'_image.sqlite')
      );
      self::$connectionList[$tablePrefix] = true;
      return;
    }
    DbConnection::connect($tablePrefix.'_image');
  }
}