<?php
class SyncShoppingImage {
  public static function execute($categoryId, $shoppingProductId, $imagePath) {
    $image = ImageDb::get($categoryId, $shoppingProductId);
    file_put_contents(
      DATA_PATH.'product_image_sync/'.$imagePath.$shoppingProductId.'.jpg', $image
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
    $path = Db::getColumn('SELECT image_path FROM product WHERE id = ?', $shoppingProductId);
    Db::update('UPDATE image_folder SET amount = amount - 1 WHERE id = ?', self::getId($path));
  }

  private static function getId($path) {
    list($levelOne, $levelTwo) = explode('/', $path);
    return $levelOne * 10000 + $levelTwo;
  }

  public static function finalize($merchantId, $categoryName, $version) {
    //TODO check dir size
    system('cd '.DATA_PATH.'product_image_staging');
    system('tar -zcf '.DATA_PATH.'product_image_sync/'.$merchantId.' '.$version.' '.$categoryName.'.tar.gz *');
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
  }
}