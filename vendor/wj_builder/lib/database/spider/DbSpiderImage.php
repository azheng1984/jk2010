<?php
class DbSpiderImage {
  private static $isConnected;

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
        'spider_image', new PDO('sqlite:'.SPIDER_IMAGE_PATH.'food_image.sqlite')
      );
      self::$isConnected = true;
      return;
    }
    DbConnection::connect('spider_image');
  }
}