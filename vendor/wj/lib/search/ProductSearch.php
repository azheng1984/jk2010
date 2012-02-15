<?php
class ProductSearch {
  private static $handler;
  private static $isEmpty;

  public static function search() {
    self::$handler = SearchService::getHandler();
    self::$isEmpty = false;
    self::setRecognition();
    self::setCategory();
    self::setPropertyList();
    self::setSort();
    self::setPriceRange();
    self::setPage();
    if (self::$isEmpty) {
      return false;
    }
    return SearchService::search(self::$handler, $GLOBALS['QUERY']['name']);
  }

  private static function setRecognition() {
    if (isset($GLOBALS['IS_RECOGNITION']) === false) {
      return;
    }
    if (isset($GLOBALS['QUERY']['id']) === false) {
      self::$isEmpty = true;
      return;
    }
    self::$handler->SetFilter('query_id', array($GLOBALS['QUERY']['id']));
  }

  private static function setCategory() {
    if (isset($GLOBALS['CATEGORY']) === false) {
      return;
    }
    if (isset($GLOBALS['CATEGORY']['id']) === false) {
      self::$isEmpty = true;
      return;
    }
    self::$handler->SetFilter('category_id', array($GLOBALS['CATEGORY']['id']));
  }

  private static function setPropertyList() {
    if (isset($GLOBALS['PROPERTY_LIST']) === false) {
      return;
    }
    foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
      if (isset($property['KEY']['mva_index']) === false) {
        self::$isEmpty = true;
        return;
      }
      self::$handler->SetFilter(
          'value_id_list_'.$property['KEY']['mva_index'],
          self::getValueIdList($property['VALUE_LIST'])
      );
    }
  }

  private static function getValueIdList($valueList) {
    $result = array();
    foreach ($valueList as $value) {
      if (isset($value['id']) === false) {
        self::$isEmpty = true;
        return;
      }
      $result[] = $value['id'];
    }
    return $result;
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