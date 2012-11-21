<?php
class ImageDb {
  private static $connectionList = array();

  public static function get($channel, $productId) {
    self::connect($channel);
    $image = Db::getColumn('image', array('product_id' => $productId));
    DbConnection::connect(DB_NAME);
    return $image;
  }

  public static function insert($channel, $productId, $image) {
    self::connect($channel);
    Db::insert('image', array('product_id' => $productId, 'image' => $image));
    DbConnection::connect(DB_NAME);
  }

  public static function update($tablePrefix, $productId, $image) {
    self::connect($tablePrefix);
    Db::update('image', array('image' => $image), 'product_id = ?', $productId);
    DbConnection::connect(DB_NAME);
  }

  public static function delete($channel, $productId) {
    self::connect($channel);
    Db::delete('image', 'product_id = ?', $productId);
    DbConnection::connect(DB_NAME);
  }

  public static function tryCreateDb($channel) {
    $path = IMAGE_PATH.'jingdong/'.$channel.'_image.sqlite';
    if (file_exists($path) === false) {
      DbConnection::connect($channel.'_image', new PDO('sqlite:'.$path));
      Db::execute('CREATE TABLE "image"'
        .'("product_id" INTEGER PRIMARY KEY NOT NULL, "image" BLOB NOT NULL)');
      DbConnection::connect(DB_NAME);
    }
  }

  public static function deleteDb($channel) {
    $path = IMAGE_PATH.'jingdong/'.$channel.'_image.sqlite';
    unlink($path);
  }

  private static function connect($channel) {
    if (isset(self::$connectionList[$channel]) === false) {
      self::tryCreateDb($channel);//TODO
      DbConnection::connect(
        $channel.'_image',
        new PDO('sqlite:'.IMAGE_PATH.'jingdong/'.$channel.'_image.sqlite')
      );
      self::$connectionList[$channel] = true;
      return;
    }
    DbConnection::connect($channel.'_image');
  }
}