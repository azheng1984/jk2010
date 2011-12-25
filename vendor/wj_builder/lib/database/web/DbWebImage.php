<?php
class DbWebImage {
  private static $isConnected;

  public static function replace($productId, $image) {
    if (self::hasImage($productId)) {
      self::insert($productId, $image);
      return;
    }
    self::update($productId, $image);
  }

  public static function delete($productId) {
    self::connect();
    $sql = 'DELETE FROM image WHERE product_id = ?';
    Db::execute($sql, $productId);
    DbConnection::connect('default');
  }

  private static function hasImage($id) {
    self::connect();
    $sql = 'SELECT product_id FROM image WHERE product_id = ?';
    $id = Db::getColumn($sql, $id);
    DbConnection::connect('default');
    return $id !== false;
  }

  private static function update($productId, $image) {
    self::connect();
    $sql = 'UPDATE image SET image = ? WHERE product_id = ?';
    Db::execute($sql, $image, $productId);
    DbConnection::connect('default');
  }

  private static function insert($productId, $image) {
    self::connectDb();
    $sql = 'INSERT INTO image(product_id, image) VALUES(?, ?)';
    Db::execute($sql, $productId, $image);
    DbConnection::connect('default');
  }

  private static function connect() {
    if (!self::$isConnected) {
      DbConnection::connect(
        'web_image', new PDO('sqlite:'.WEB_IMAGE_PATH.'image.sqlite')
      );
      self::$isConnected = true;
      return;
    }
    DbConnection::connect('web_image');
  }
}