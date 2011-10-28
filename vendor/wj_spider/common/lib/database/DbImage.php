<?php
class DbImage {
  public static function createTable($tablePrefix) {
    if (!file_exists($tablePrefix.'_image.sqlite')) {
      DbConnection::connect(
        $tablePrefix.'_image', new PDO('sqlite:'.$tablePrefix.'_image.sqlite')
      );
      $sql = 'CREATE  TABLE "main"."image" '
        .'("product_id" INTEGER PRIMARY KEY  NOT NULL , "image" BLOB NOT NULL )';
      Db::execute($sql);
      DbConnection::connect('defalut');
    }
  }

  public static function deleteImage($tablePrefix, $productId) {
    DbConnection::connect(
        'image', new PDO('sqlite:'.$tablePrefix.'_image.sqlite')
    );
    $sql = 'DELETE FROM image WHERE ';
  }

  public static function insertImage($tablePrefix, $productId) {
    
  }

  public static function updateImage($tablePrefix, $productId) {
    
  }
}