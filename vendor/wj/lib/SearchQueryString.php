<?php
class SearchQueryString {
  public static function parse() {
    self::parseSort();
    self::parsePriceRange();
    $GLOBALS['QUERY_STRING'] = self::get();
  }

  public static function get($sort = null) {
    $parameterList = array();
    if ($GLOBALS['SORT'] !== 'sale_rank') {
      $parameterList[] = 'sort='.$sort;
    }
    if (isset($GLOBALS['PRICE_FROM'])) {
      $parameterList[] = 'price_from='.$GLOBALS['PRICE_FROM'];
    }
    if (isset($GLOBALS['PRICE_TO'])) {
      $parameterList[] = 'price_to='.$GLOBALS['PRICE_TO'];
    }
    if (count($parameterList) !== 0) {
      return '?'.implode('&', $parameterList);
    }
    return '';
  }

  private static function parseSort() {
    if (isset($_GET['sort']) && in_array(
      $_GET['sort'], array('time', 'discount', 'price', '-price'))) {
      $GLOBALS['SORT'] = $_GET['sort'];
      return;
    }
    $GLOBALS['SORT'] = 'sale_rank';
  }

  private static function parsePriceRange() {
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])
        && $_GET['price_from'] >= 0) {
      $GLOBALS['PRICE_FROM'] = $_GET['price_from'];
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])
        && $_GET['price_to'] >= 0) {
      $GLOBALS['PRICE_TO'] = $_GET['price_to'];
    }
  }
}