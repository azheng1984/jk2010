<?php
class ImageSyncFile {
  private static $syncFileName = null;

  public static function initialize(
    $taskId, $merchantId, $categoryId, $version
  ) {
    self::$syncFileName =
      $taskId.'_'.$merchantId.'_'.$categoryId.'_'.$version.'.tar.gz';
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
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
    self::clean();
  }

  public static function clean() {
    system('rm -rf '.DATA_PATH.'product_image_staging');
    system('mkdir '.DATA_PATH.'product_image_staging');
  }
}