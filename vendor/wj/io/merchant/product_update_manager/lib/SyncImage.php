<?php
class SyncImage {
  public static function bind(
    $categoryId, $shoppingProductId, $productId, $imagePath
  ) {
    $image = ImageDb::get($categoryId, $productId);
    $dir = DATA_PATH.'product_image_staging/'.$imagePath;
    if (is_dir($dir)) {
      mkdir($dir, 0755, true);
    }
    file_put_contents($dir.'/'.$shoppingProductId.'.jpg', $image);
  }

  public static function delete($imagePath) {
    list($levelOne, $levelTwo) = explode('/', $imagePath);
    $id = $levelOne * 10000 + $levelTwo;
    Db::update(
      'UPDATE image_folder SET amount = amount - 1 WHERE id = ?', $id
    );
  }

  public static function allocateImageFolder() {
    DbConnection::connect('default');
    $row = Db::getRow('SELECT * FROM image_folder ORDER BY amount LIMIT 1');
    if ($row === false || $row['amount'] >= 10000) {
      Db::insert('image_folder', array());
      return Db::getLastInsertId();
    }
    Db::update(
    'image_folder', array('amount' => ++$row['amount']), 'id = ?', $row['id']
    );
    DbConnection::close();
    $levelOne = floor($row['id'] / 10000);
    $levelTwo = $row['id'] % 10000;
    return $levelOne.'/'.$levelTwo;
  }
}