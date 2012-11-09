<?php
class SyncShoppingImage {
  public static function execute($categoryId, $shoppingProductId, $imagePath) {
    $image = ImageDb::get($categoryId, $shoppingProductId);
    $imageStagingFolder = '/home/azheng/image_staging/jingdong/';
    file_put_contents(
      $imageStagingFolder.$imagePath.$shoppingProductId.'.jpg', $image
    );
    return $imagePath;
  }

  public static function getImagePath() {
    $id = $this->getImageFolder();
    $levelOne = floor($id / 10000);
    $folder = $levelOne;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    $levelTwo = $id % 10000;
    $folder = $folder.'/'.$levelTwo;
    if (is_dir($folder)) {
      mkdir($folder);
    }
    return $folder;
  }

  private static function getImageFolder() {
    DbConnection::connect('io_merchant_spider');
    $row = Db::getRow('SELECT * FROM image_folder ORDER BY amount LIMIT 1');
    if ($row === false || $row['amount'] >= 10000) {
      Db::insert('image_folder', array());
      return Db::getLastInsertId();
    }
    Db::update(
      'image_folder', array('amount' => ++$row['amount']), 'id = ?', $row['id']
    );
    return $row['id'];
  }
}