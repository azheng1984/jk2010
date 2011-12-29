<?php
class DbImage {
  private static $isConnected;
  private static $isWebConnected;

  public static function get($id) {
    self::connect();
    $sql = 'SELECT image FROM image WHERE product_id = ?';
    $image = Db::getColumn($sql, $id);
    DbConnection::connect('default');
    return $image;
  }

  private static function connect() {
    if (!self::$isConnected) {
      DbConnection::connect(
        'image', new PDO('sqlite:'.WEB_IMAGE_PATH.'image.sqlite')
      );
      self::$isConnected = true;
      return;
    }
    DbConnection::connect('image');
  }
}