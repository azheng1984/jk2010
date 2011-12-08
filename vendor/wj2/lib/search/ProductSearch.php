<?php
class ProductSearch {
  private static $sphinx;

  public static function search() {
    self::initialize();
    self::setCategory();
    self::setProperties();
    self::setModel();
    self::setPriceRange();
    self::setSortMode();
    self::setPage();
    return self::getResult();
  }

  private static function initialize() {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    self::$sphinx = $sphinx;
  }

  private static function setCategory() {
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      self::$sphinx->SetFilter(
        'category_id', array($GLOBALS['URI']['CATEGORY']['id'])
      );
    }
  }

  private static function setProperties() {
    if (isset($GLOBALS['URI']['PROPERTIES'])) {
      foreach ($GLOBALS['URI']['PROPERTIES'] as $item) {
        self::$sphinx->SetFilter(
          'value_id_list_'.$item['KEY']['mva_index'],
          array($item['VALUE']['id'])
        );
      }
    }
  }

  private static function setModel() {
    if (isset($GLOBALS['URI']['MODEL'])) {
      self::$sphinx->SetFilter(
        'model_id', array($GLOBALS['URI']['MODEL']['id'])
      );
    }
  }

  private static function setPriceRange() {
    if (isset($GLOBALS['URI']['PRICE'])) {
      self::$sphinx->SetFilterRange(
        'lowest_price_x_100',
        $GLOBALS['URI']['PRICE']['LOWEST'] * 100,
        $GLOBALS['URI']['PRICE']['HIGHEST'] * 100
      );
    }
  }

  private static function setSortMode() {
    $sort = 'sale_rank';
    if (isset($GLOBALS['URI']['SORT'])) {
      $mapping = array(
        '价格' => 'lowest_price_x_100',
        '-价格' => 'lowest_price_x_100',
        '销量' => 'sale_rank',
        '上架时间' => 'publish_timestamp',
        '折扣' => 'discount_x_10'
      );
      $sort = $mapping[$GLOBALS['URI']['SORT']];
    }
    $mode = SPH_SORT_ATTR_ASC;
    if ($sort === 'lowest_price_x_100' && $GLOBALS['URI']['SORT'] === '-价格') {
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

  private static function getResult() {
    if (!isset($GLOBALS['URI']['QUERY'])) {
      throw new NotFoundException;
    }
    $result = self::$sphinx->query($GLOBALS['URI']['QUERY'], 'wj_search');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}