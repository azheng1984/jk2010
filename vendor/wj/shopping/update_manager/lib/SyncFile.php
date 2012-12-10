<?php
class SyncFile {
  private static $commandFileName = null;
  private static $commandZipFileName = null;
  private static $imageZipFileName = null;

  public static function execute($task) {
    $suffix = $task['id'].'_'
      .$task['merchant_id'].'_'.$task['category_id'].'_'.$task['version'];
    self::$commandFileName = $suffix;
    self::$imageZipFileName = $suffix.'.tar.gz';
    self::$commandZipFileName = $suffix.'.gz';
    self::getFile(self::$imageZipFileName);
    self::getFile(self::$commandZipFileName);
    chdir(DATA_PATH.'sync');
    //TODO clean sync
    self::system('gzip -df '.self::$commandZipFileName);
    chdir(IMAGE_PATH);
    self::system(
      'tar -zxf '.DATA_PATH.'sync/'.self::$imageZipFileName
    );
  }

  public static function system($command) {
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
      $ftp = ftp_connect("127.0.0.1");
      if ($ftp !== false) {
        break;
      }
      sleep(10);
    }
    while (ftp_login($ftp, "t", "t") === false) {
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

  public static function finialize() {
    unlink(self::getCommandFilePath());
    unlink(self::getCommandFilePath().'.tar.gz');
    self::$commandFileName = null;
    self::$commandZipFileName = null;
    self::$imageZipFileName = null;
  }
}