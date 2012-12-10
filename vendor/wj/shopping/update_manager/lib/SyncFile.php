<?php
class SyncFile {
  private static $comandFileName = null;
  private static $comandZipFileName = null;
  private static $imageZipFileName = null;

  public static function execute($task) {
    $suffix = $task['merchant_id'].'_'.$task['category_id'].'_'.$task['version'];
    self::$commandFileName = $suffix;
    self::$imageZipFileName = $suffix.'.gz';
    self::$comandZipFileName = $suffix.'.tar.gz';
    $this->getFile(self::$imageZipFileName);
    $this->getFile(self::$comandZipFileName);
    self::system('cd '.DATA_PATH.'sync');
    self::system('gzip -d '.self::$comandZipFileName);
    self::system('cd '.IMAGE_PATH);
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
    return DATA_PATH.'sync/'.self::$comandFileName;
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
    if (ftp_get(
      $ftp, DATA_PATH.'sync/'.$fileName, $fileName, FTP_BINARY) === false
    ) {
      ftp_close($ftp);
      $this->getFile();
      sleep(10);
    }
    ftp_close($ftp);
  }

  public static function finialize() {
    unlink(DATA_PATH.'sync/'.self::$comandFileName);
    self::$comandFileName = null;
    self::$comandZipFileName = null;
    self::$imageZipFileName = null;
  }
}