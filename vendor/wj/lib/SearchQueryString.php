<?php
class SearchQueryString {
  public static function initialize() {
    if (isset($_GET['sort'])
      && in_array($_GET['sort'], array('time', 'discount', 'price', '-price'))
    ) {
      $sort = $_GET['sort'];
    }
    $list = array('time', 'discount', 'price', '-price');
    $GLOBALS['QUERY_STRING'] = self::get();
  }

  public static function get($sort = null) {
    if ($sort === null && isset($_GET['sort'])) {
      $sort = $_GET['sort'];
    }
    $parameterList = array();
    if (in_array($sort, array('time', 'discount', 'price', '-price'))) {
      $parameterList[] = 'sort='.$sort;
    }
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])
      && $_GET['price_from'] >= 0) {
      $parameterList[] = 'price_from='.$_GET['price_from'];
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])
      && $_GET['price_to'] >= 0) {
      $parameterList[] = 'price_to='.$_GET['price_to'];
    }
    if (count($parameterList) !== 0) {
      return '?'.implode('&', $parameterList);
    }
    return '';
  }

  private static function parseSort() {
    
  }

  private static function parsePriceRange() {
    
  }
}