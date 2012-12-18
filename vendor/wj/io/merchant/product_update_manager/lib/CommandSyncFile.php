<?php
class CommandSyncFile {
  private static $portalSyncFilePath = null;
  private static $fileNameSuffix = null;
  private static $portalSyncFile = null;
  private static $previousCommand = null;

  public static function initialize($taskId) {
    self::$previousCommand = null;
    self::$fileNameSuffix = $taskId;
    self::$portalSyncFilePath =
      DATA_PATH.'command_staging/'.self::$fileNameSuffix.'.sync';
    self::$portalSyncFile = fopen(self::$portalSyncFilePath, 'w');
    if (self::$portalSyncFile === false) {
      throw new Exception;
    }
  }

  public static function insertProduct($product, $id) {
    $output = '';
    if (self::$previousCommand !== 'p') {
      self::$previousCommand = 'p';
      $output = "p\n";
    }
    $output .= $id."\n";
    $output .= $product['uri_argument_list']."\n";
    $output .= $product['image_path']."\n";
    $output .= $product['image_digest']."\n";
    $output .= $product['title']."\n";
    $output .= $product['price_from_x_100']."\n";
    $output .= $product['price_to_x_100']."\n";
    $output .= $product['agency_name']."\n";
    $output .= $product['keyword_list']."\n";
    self::outputForPortal($output);
  }

  public static function updateProduct($id, $replacementColumnList) {
    $output = '';
    if (self::$previousCommand !== 'u') {
      self::$previousCommand = 'u';
      $output = "u\n";
    }
    $output .= $id."\n";
    if (isset($replacementColumnList['uri_argument_list'])) {
      $output .= "\n0\n".$replacementColumnList['uri_argument_list'];
    }
    if (isset($replacementColumnList['image_digest'])) {
      $output .= "\n1\n".$replacementColumnList['image_digest'];
    }
    if (isset($replacementColumnList['title'])) {
      $output .= "\n2\n".$replacementColumnList['title'];
    }
    if (isset($replacementColumnList['price_from_x_100'])) {
      $output .= "\n3\n".$replacementColumnList['price_from_x_100'];
    }
    if (isset($replacementColumnList['price_to_x_100'])) {
      $output .= "\n4\n".$replacementColumnList['price_to_x_100'];
    }
    if (isset($replacementColumnList['agency_name'])) {
      $output .= "\n5\n".$replacementColumnList['agency_name'];
    }
    if (isset($replacementColumnList['keyword_list'])) {
      $output .= "\n6\n".$replacementColumnList['keyword_list'];
    }
    self::outputForPortal($output."\n\n");
  }

  public static function deleteProduct($id) {
    $command = '';
    if (self::$previousCommand !== 'd') {
      self::$previousCommand = 'd';
      $command = "d\n";
    }
    self::outputForPortal($command.$id."\n");
  }

  private static function outputForPortal($content) {
    fwrite(self::$portalSyncFile, $content);
  }

  public static function finalize() {
    $isUpdate = false;
    fclose(self::$portalSyncFile);
    if (filesize(self::$portalSyncFilePath) !== 0) {
      $isUpdate = true;
      self::system(
        'tar -zcf '.FTP_PATH.self::$fileNameSuffix.'.tar.gz'
          .' -C '.DATA_PATH.'command_staging '.self::$fileNameSuffix.'.sync'
      );
    }
    unlink(self::$portalSyncFilePath);
    return $isUpdate;
  }

  public static function clean() {
    self::system('rm -rf '.DATA_PATH.'command_staging');
    self:system('mkdir '.DATA_PATH.'command_staging');
  }

  private static function system($command) {
    $return = null;
    system($command, $return);
    if ($return !== 0) {
      throw new Exception;
    }
  }
}