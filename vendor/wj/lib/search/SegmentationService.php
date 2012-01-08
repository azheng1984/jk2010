<?php
class SegmentationService {
  public function execute($input, $retry = 3) {
    $output = '';
    $handler = fsockopen("127.0.0.1", 8080, $errno, $errstr, 10);
    if (!$handler) {
      return $this->execute($input, --$retry);
    }
    fwrite($handler, $input);
    while (!feof($handler)) {
      $output .= fgets($handler, 256);
    }
    fclose($handler);
    return $output;
  }
}