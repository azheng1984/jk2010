<?php
class SyncShoppingImage {
  private static $syncFileName = null;

  public static function initialize(
    $taskId, $merchantId, $categoryId, $version
  ) {
    self::$syncFileName =
      $taskId.'_'.$merchantId.'_'.$categoryId.'_'.$version.'.tar.gz';
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
    if (file_exists(DATA_PATH.'product_image_sync/'.self::$syncFileName)) {
      system('rm '.DATA_PATH.'product_image_sync/'.self::$syncFileName);
    }
  }

  public static function execute(
    $categoryName, $shoppingProductId, $productId, $imagePath
  ) {
    $image = ImageDb::get($categoryName, $productId);
    file_put_contents(
      DATA_PATH.'product_image_staging/'.$imagePath.'/'.$shoppingProductId.'.jpg', $image
    );
    return $imagePath;
  }

  public static function getImagePath() {
    $id = self::getImageFolder();
    $levelOne = floor($id / 10000);
    $folder = $levelOne;
    if (is_dir(DATA_PATH.'product_image_staging/'.$folder) === false) {
      mkdir(DATA_PATH.'product_image_staging/'.$folder);
    }
    $levelTwo = $id % 10000;
    $folder = $folder.'/'.$levelTwo;
    if (is_dir(DATA_PATH.'product_image_staging/'.$folder) === false) {
      mkdir(DATA_PATH.'product_image_staging/'.$folder);
    }
    return $folder;
  }

  private static function getImageFolder() {
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
    return $row['id'];
  }

  public static function delete($imagePath) {
    Db::update(
      'UPDATE image_folder SET amount = amount - 1 WHERE id = ?',
      self::getId($imagePath)
    );
  }

  private static function getId($path) {
    list($levelOne, $levelTwo) = explode('/', $path);
    return $levelOne * 10000 + $levelTwo;
  }

  public static function finalize() {
    $dirList = array();
    $dir = dir(DATA_PATH.'product_image_staging');
    $hasDir = false;
    while (false !== ($entry = $dir->read())) {
      if ($entry !== '.' && $entry !== '..') {
        $dirList[] = $entry;
      }
    }
    if (count($dirList) === 0) {
      return;
    }
    system(
      'tar -zcf '.DATA_PATH.'product_image_sync/'
        .self::$syncFileName.' -C '.DATA_PATH.'product_image_staging '
        .implode(' ', $dirList)
    );
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
  }

  public static function clean() {
    
  }
}