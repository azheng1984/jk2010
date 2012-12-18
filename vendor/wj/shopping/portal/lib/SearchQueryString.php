<?php
class SearchQueryString {
  public static function parse() {
    self::parseSort();
    self::parsePriceRange();
    $GLOBALS['QUERY_STRING'] = self::get();
  }

  public static function get($sort = null) {
    $parameterList = array();
    if ($sort === null) {
      $sort = $GLOBALS['SORT'];
    }
    if ($sort !== 'popularity_rank') {
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
    if (isset($_GET['sort']) 
      && in_array($_GET['sort'], array('price', '-price'))
    ) {
      $GLOBALS['SORT'] = $_GET['sort'];
      return;
    }
    $GLOBALS['SORT'] = 'popularity_rank';
  }

  //TODO: price_from/to 为 ''
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