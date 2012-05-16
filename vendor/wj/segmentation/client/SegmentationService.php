<?php
class SegmentationService {
  private static $cache = array();

  public static function execute($input, $retry = 2) {
    if (isset(self::$cache[$input])) {
      return self::$cache[$input];
    }
    $handler = fsockopen("127.0.0.1", 8080, $errno, $errstr, 10);
    if ($handler === false && $retry === 0) {
      return $input;
    }
    if ($handler === false) {
      return self::execute($input, --$retry);
    }
    fwrite($handler, $input);
    $output = '';
    while (feof($handler) === false) {
      $output .= fgets($handler, 256);
    }
    fclose($handler);
    self::$cache[$input] = $output;
    return $output;
  }
}