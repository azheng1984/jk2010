<?php
class ProductSearch {
  private static $sphinx;

  public static function search() {
    self::initialize();
    self::setCategory();
    self::setPropertyList();
    self::setModel();
    self::setPriceRange();
    self::setSortMode();
    self::setPage();
    return self::query();
  }

  private static function initialize() {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    self::$sphinx = $sphinx;
  }

  private static function setCategory() {
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      self::$sphinx->SetFilter(
        'category_id', array($GLOBALS['URI']['CATEGORY']['id'])
      );
    }
  }

  private static function setPropertyList() {
    if (isset($GLOBALS['URI']['PROPERTY_LIST'])) {
      foreach ($GLOBALS['URI']['PROPERTY_LIST'] as $property) {
        self::$sphinx->SetFilter(
          'value_id_list_'.$property['KEY']['mva_index'],
          self::getValueIdList($property['VALUE_LIST'])
        );
      }
    }
  }

  private static function getValueIdList($valueList) {
    $result = array();
    foreach ($valueList as $value) {
      if (isset($value['id'])) { //no product
        $result[] = $value['id'];
      }
    }
    return $result;
  }

  private static function setModel() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      self::$sphinx->SetFilter(
        'model_id', array($_GET['id'])
      );
    }
  }

  private static function setPriceRange() {
    $range = self::getPriceRange();
    if ($range !== null) {
      self::$sphinx->SetFilterRange(
        'lowest_price_x_100', $range['min'], $range['max']
      );
    }
  }

  private static function getPriceRange() {
    $priceFrom = 0;
    $priceTo = 0;
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])) {
      $priceFrom = $_GET['price_from'] * 100;
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {
      $priceTo = $_GET['price_to'] * 100;
    }
    if ($priceFrom === 0 && $priceTo === 0) {
      return;
    }
    if ($priceFrom > $priceTo) {
      return array('min' => $priceTo, 'max' => $priceFrom);
    }
    return array('min' => $priceFrom, 'max' => $priceTo);
  }

  private static function setSortMode() {
    $sort = 'sale_rank';
    $mapping = array(
      'price' => 'lowest_price_x_100',
      '-price' => 'lowest_price_x_100',
      'time' => 'publish_timestamp',
      'discount' => 'discount_x_10'
    );
    if (isset($_GET['sort']) && isset($mapping[$_GET['sort']])) {
      $sort = $mapping[$_GET['sort']];
    }
    $mode = SPH_SORT_ATTR_ASC;
    if ($sort === 'lowest_price_x_100' && $_GET['sort'] === '-价格') {
      $mode = SPH_SORT_ATTR_DESC;
    }
    self::$sphinx->SetSortMode($mode, $sort);
  }

  private static function setPage() {
    $page = 1;
    if (isset($GLOBALS['URI']['PAGE'])) {
      $page = $GLOBALS['URI']['PAGE'];
    }
    $offset = ($page - 1) * 16;
    self::$sphinx->SetLimits($offset, 16);
  }

  private static function query() {
    if (!isset($GLOBALS['URI']['QUERY'])) {
      throw new NotFoundException;
    }
    $query = SegmentationService::execute($GLOBALS['URI']['QUERY']);
    $result = self::$sphinx->query($query, 'wj_product');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}