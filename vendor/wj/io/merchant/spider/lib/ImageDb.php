<?php
class ImageDb {
  private static $connection = null;
  private static $categoryName = null;

  public static function get($categoryName, $productId) {
    self::connect($categoryName);
    $image = Db::getColumn('image', array('product_id' => $productId));
    self::close();
    return $image;
  }

  public static function insert($categoryName, $productId, $image) {
    self::connect($categoryName);
    Db::insert('image', array('product_id' => $productId, 'image' => $image));
    self::close();
  }

  public static function update($categoryName, $productId, $image) {
    self::connect($categoryName);
    Db::update('image', array('image' => $image), 'product_id = ?', $productId);
    self::close();
  }

  public static function delete($categoryName, $productId) {
    self::connect($categoryName);
    Db::delete('image', 'product_id = ?', $productId);
    self::close();
  }

  public static function deleteDb($categoryName) {
    $path = IMAGE_PATH.'jingdong/'.$categoryName.'.sqlite';
    if (file_exists($path)) {
      unlink($path);
    }
  }

  private static function connect($categoryName) {
    if (self::$categoryName === $categoryName) {
      return DbConnection::connect(null, self::$connection);
    }
    $path = IMAGE_PATH.'jingdong/'.$categoryName.'.sqlite';
    $hasFile = file_exists($path);
    $pdo = new PDO('sqlite:'.$path);
    DbConnection::connect(null, $pdo);
    if ($hasFile === false) {
      Db::execute('CREATE TABLE "image"'
        .'("product_id" INTEGER PRIMARY KEY NOT NULL, "image" BLOB NOT NULL)');
    }
    self::$categoryName = $categoryName;
    self::$connection = $pdo;
  }

  private static function close() {
    DbConnection::close();
  }
}