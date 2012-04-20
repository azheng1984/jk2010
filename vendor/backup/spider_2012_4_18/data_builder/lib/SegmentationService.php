<?php
class SegmentationService {
  public static function execute($input, $retry = 2) {
    $output = '';
    $handler = fsockopen("127.0.0.1", 8080, $errno, $errstr, 10);
    if (!$handler && $retry === 0) {
      return $output;
    }
    if (!$handler) {
      return self::execute($input, --$retry);
    }
    fwrite($handler, $input);
    while (!feof($handler)) {
      $output .= fgets($handler, 256);
    }
    fclose($handler);
    return $output;
  }
}