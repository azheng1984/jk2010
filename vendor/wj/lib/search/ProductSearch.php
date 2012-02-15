<?php
class ProductSearch {
  private static $handler;

  public static function search() {
    self::$handler = SearchService::getHandler();
      if (SearchService::setRecognition(self::$handler) === false) {
      return false;
    }
    if (SearchService::setCategory(self::$handler) === false) {
      return false;
    }
      if (SearchService::setPropertyList(self::$handler) === false) {
      return false;
    }
    self::setSort();
    self::setPriceRange();
    self::setPage();
    if (self::$isEmpty) {
      return false;
    }
    return SearchService::search(self::$handler, $GLOBALS['QUERY']['name']);
  }

  private static function setSort() {
    $mapping = array(
      'sale_rank' => 'sale_rank',
      'price' => 'lowest_price_x_100',
      '-price' => 'lowest_price_x_100',
      'time' => 'publish_timestamp',
      'discount' => 'discount_x_10'
    );
    $mode = SPH_SORT_ATTR_ASC;
    if ($GLOBALS['SORT'] === '-price') {
      $mode = SPH_SORT_ATTR_DESC;
    }
    self::$handler->SetSortMode($mode, $mapping[$GLOBALS['SORT']]);
  }

  private static function setPriceRange() {
    $max = 0;
    $min = 0;
    if (isset($GLOBALS['PRICE_FROM'])) {
      $max = $GLOBALS['PRICE_FROM'] * 100;
    }
    if (isset($GLOBALS['PRICE_TO'])) {
      $min = $GLOBALS['PRICE_TO'] * 100;
    }
    if ($max === 0 && $min === 0) {
      return;
    }
    if ($min > $max) {
      list($min, $max) = array($max, $min);
    }
    self::$handler->SetFilterRange('lowest_price_x_100', $min, $max);
  }

  private static function setPage() {
    $offset = ($GLOBALS['PAGE'] - 1) * 16;
    self::$handler->SetLimits($offset, 16);
  }
}