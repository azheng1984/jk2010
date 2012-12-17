<?php
class ImageSyncFile {
  private static $syncFileName = null;

  public static function initialize(
    $taskId, $merchantId, $categoryId, $version
  ) {
    self::$syncFileName =
      $taskId.'_'.$merchantId.'_'.$categoryId.'_'.$version.'.image.tar.gz';
    self::system('rm -rf '.DATA_PATH.'product_image_staging');
    self::system('mkdir '.DATA_PATH.'product_image_staging');
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
      copy(DATA_PATH.'empty.tar.gz', FTP_PATH.self::$syncFileName);
      return;
    }
    self::system(
      'tar -zcf '.FTP_PATH
        .self::$syncFileName.' -C '.DATA_PATH.'product_image_staging '
        .implode(' ', $dirList)
    );
    self::clean();
  }

  public static function clean() {
    self::system('rm -rf '.DATA_PATH.'product_image_staging');
    self::system('mkdir '.DATA_PATH.'product_image_staging');
  }

  private static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }
}