<?php
class DbImage {
  public static function createTable($tablePrefix) {
    if (file_exists($tablePrefix.'_image.sqlite')) {
      DbConnection::connect(
        'image', new PDO('sqlite:'.$tablePrefix.'_image.sqlite')
      );
      $sql = 'CREATE  TABLE "main"."image" '
        .'("product_id" INTEGER PRIMARY KEY  NOT NULL , "image" BLOB NOT NULL )';
      Db::execute($sql);
      DbConnection::connect('defalut');
    }
  }
}