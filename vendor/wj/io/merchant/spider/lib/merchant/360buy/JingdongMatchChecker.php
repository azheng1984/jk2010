<?php
class JingdongMatchChecker {
  private static $count = null;
  private static $processor = null;
  private static $path = null;
  private static $needle = null;
  private static $needle2 = null;
  private static $needle3 = null;//电信拦截

  public static function execute($processor, $path, $html) {
    if (self::$needle === null) {
      self::$needle = iconv('utf-8', 'gbk', '<title>京东网上商城');
      self::$needle2 = iconv('utf-8', 'gbk', '您请求的页面现在无法打开');
      self::$needle3 = '<title></title>';
    }
    if (strpos($html, self::$needle) === false
      && strpos($html, self::$needle2) === false
      && strpos($html, self::$needle3) === false) {
      return false;
    }
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