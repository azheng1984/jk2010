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

  public static function delete($shoppingProductId) {
    //TODO:删除 image,更新 path 数据库
  }

  public static function finalize() {
    //TODO:压缩 & 移动图片文件夹到 ftp 服务器
    system('zip');
  }
}