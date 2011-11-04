<?php
class DbImage {
  private static $isConnected;
  private static $isWebConnected;

  public static function get($id) {
    self::connect();
    $sql = 'SELECT image FROM image WHERE product_id = ?';
    Db::execute($sql, $id);
    DbConnection::connect('default');
  }

  public static function updateWebImage($webProductId, $image) {
    self::connectWebDb();
    $sql = 'UPDATE image SET image = ? WHERE product_id = ?';
    Db::execute($sql, $image, $webProductId);
    DbConnection::connect('default');
  }

  public static function insertWebImage($webProductId, $image) {
    self::connectWebDb();
    $sql = 'INSERT INTO image(product_id, image) VALUES(?, ?)';
    Db::execute($sql, $webProductId, $image);
    DbConnection::connect('default');
  }

  public static function deleteWebImage($webProductId) {
    self::connectWebDb();
    $sql = 'DELETE FROM image WHERE product_id = ?';
    Db::execute($sql, $webProductId);
    DbConnection::connect('default');
  }

  private static function connectWebDb() {
    if (!self::$isWebConnected) {
      DbConnection::connect(
        'web_image', new PDO('sqlite:'.IMAGE_PATH.'image.sqlite')
      );
      self::$isWebConnected = true;
      return;
    }
    DbConnection::connect('web_image');
  }

  private static function connect() {
    if (!self::$isConnected) {
      DbConnection::connect(
        'image', new PDO('sqlite:'.IMAGE_PATH.'food_image.sqlite')
      );
      self::$isConnected = true;
      return;
    }
    DbConnection::connect('image');
  }
}