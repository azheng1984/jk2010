<?php
class SyncFile {
  private static $commandFileName = null;
  private static $fileNameSuffix = null;
  private static $commandZipFileName = null;
  private static $imageZipFileName = null;

  public static function initialize($task) {
    $suffix = $task['id'].'_'.$task['merchant_id']
      .'_'.$task['category_id'].'_'.$task['version'];
    self::$fileNameSuffix = $suffix;
    self::$commandFileName = $suffix.'.sync';
    self::$imageZipFileName = $suffix.'.image.tar.gz';
    self::$commandZipFileName = $suffix.'.tar.gz';
  }

  public static function execute() {
    self::getFile(self::$imageZipFileName);
    self::getFile(self::$commandZipFileName);
    self::system('tar -zxf '.DATA_PATH.'sync/'.self::$commandZipFileName
      .' -C '.DATA_PATH.'sync/');
    self::system(
      'tar -zxf '.DATA_PATH.'sync/'.self::$imageZipFileName.' -C '.IMAGE_PATH
    );
  } 

  private static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }

  public static function getCommandFilePath() {
    return DATA_PATH.'sync/'.self::$commandFileName;
  }

  private static function getFile($fileName) {
    $ftp = null;
    for (;;) {
      $ftp = ftp_connect('127.0.0.1');
      if ($ftp !== false) {
        break;
      }
      sleep(10);
    }
    while (ftp_login($ftp, 't', 't') === false) {
      sleep(10);
    }
    $result = ftp_get($ftp, DATA_PATH.'sync/'.$fileName, $fileName, FTP_BINARY);
    if ($result === false) {
      ftp_close($ftp);
      sleep(10);
      self::getFile($fileName);
      return;
    }
    ftp_close($ftp);
  }

  public static function remove() {
    unlink(self::getCommandFilePath());
    $filePathSuffix = DATA_PATH.'sync/'.self::$fileNameSuffix;
    unlink($filePathSuffix.'.tar.gz');
    unlink($filePathSuffix.'.image.tar.gz');
  }
}