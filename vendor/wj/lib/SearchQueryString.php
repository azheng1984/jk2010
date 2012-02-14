<?php
class SearchQueryString {
  public static function initialize() {
    
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
}