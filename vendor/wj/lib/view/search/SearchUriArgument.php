<?php
class SearchUriArgument {
  private static $current;

  public static function get($sort = null) {
    $parameterList = array();
    if ($sort !== '销量') {
      $parameterList[] = 'sort='.$sort;
    }
      if (isset($_GET['price_from'])) {
      $parameterList[] = 'price_from='.$_GET['price_from'];
    }
    if (isset($_GET['price_to'])) {
      $parameterList[] = 'price_to='.$_GET['price_to'];
    }
    if (count($parameterList) !== 0) {
      return '?'.implode('&', $parameterList);
    }
    return '';
  }

  public static function getCurrent() {
    if (self::$current === null) {
      $sort = isset($_GET['sort']) ? $_GET['sort'] : '销量';
      self::$current = self::get($sort);
    }
    return self::$current;
  }
}