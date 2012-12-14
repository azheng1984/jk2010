<?php
class JingdongMatchChecker {
  private static $count = null;
  private static $processor = null;
  private static $path = null;

  public static function execute($processor, $path, $html) {
    if (strpos($html, '{<title>京东网上商城') === false) {
      return false;
    }
    echo 'OK~';
    exit;
    if (self::$processor !== $processor || self::$path !== $path) {
      self::$processor = $processor;
      self::$path = $path;
      self::$count = 0;
    }
    if (self::$count === 10) {
      return false;
    }
    ++self::$count;
    sleep(3);
    $class = 'Jingdong'.$processor.'Processor';
    $processor = new $class;
    $processor->execute($path);
  }
}