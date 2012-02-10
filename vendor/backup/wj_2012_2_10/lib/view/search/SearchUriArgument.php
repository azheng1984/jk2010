<?php
class SearchUriArgument {
  private static $current;

  public static function get($sort = null) {
    $parameterList = array();
    $mapping = array('上架时间' => 'time', '折扣' => 'discount', '价格' => 'price', '-价格' => '-price');
    if ($sort !== '销量') {
      $parameterList[] = 'sort='.$mapping[$sort];
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
      $mapping = array('time' => '上架时间', 'discount' => '折扣', 'price' => '价格', '-price' => '价格');
      $sort = isset($_GET['sort']) ? $mapping[$_GET['sort']] : '销量';
      self::$current = self::get($sort);
    }
    return self::$current;
  }
}