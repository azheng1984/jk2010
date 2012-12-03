<?php
class SyncShoppingImage {
  private static $syncFileName = null;

  public static function initialize($merchantId, $categoryId, $version) {
    self::$syncFileName = $merchantId.' '.$categoryId.' '.$version.'.tar.gz';
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
    if (file_exists(DATA_PATH.'product_image_sync/'.self::$syncFileName)) {
      system('rm '.DATA_PATH.'product_image_sync/'.self::$syncFileName);
    }
  }

  public static function execute($categoryId, $shoppingProductId, $imagePath) {
    $image = ImageDb::get($categoryId, $shoppingProductId);
    file_put_contents(
      DATA_PATH.'product_image_staging/'.$imagePath.'/'.$shoppingProductId.'.jpg', $image
    );
    echo DATA_PATH.'product_image_staging/'.$imagePath.'/'.$shoppingProductId.'.jpg';
    return $imagePath;
  }

  public static function getImagePath() {
    $id = $this->getImageFolder();
    $levelOne = floor($id / 10000);
    $folder = levelOne;
    if (is_dir(DATA_PATH.'product_image_staging/'.$folder)) {
      mkdir(DATA_PATH.'product_image_staging/'.$folder);
    }
    $levelTwo = $id % 10000;
    $folder = $folder.'/'.$levelTwo;
    if (is_dir(DATA_PATH.'product_image_staging/'.$folder)) {
      mkdir(DATA_PATH.'product_image_staging/'.$folder);
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

  public static function finalize() {
    $dir = dir(DATA_PATH.'product_image_staging');
    $hasDir = false;
    while (false !== ($entry = $dir->read())) {
      if ($entry !== '.' && $entry !== '..') {
        $hasDir = true;
        break;
      }
    }
    if ($hasDir === false) {
      return;
    }
    system('cd '.DATA_PATH.'product_image_staging');
    system('tar -zcf '.DATA_PATH.'product_image_sync/'.self::$syncFileName.' *');
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
  }
}