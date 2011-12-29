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
          array($item['VALUES'][0]['id'])
        );
      }
    }
  }

  private static function setModel() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      self::$sphinx->SetFilter(
        'model_id', array($_GET['id'])
      );
    }
  }

  private static function setPriceRange() {
    $priceFrom = null;
    $priceTo = null;
    if (isset($_GET['price_from']) && is_numeric($_GET['price_from'])) {
      $priceFrom = $_GET['price_from'];
    }
    if (isset($_GET['price_to']) && is_numeric($_GET['price_to'])) {
      $priceTo = $_GET['price_to'];
    }
    if ($priceFrom !== null || $priceTo !== null) {
      self::$sphinx->SetFilterRange('lowest_price_x_100',$priceFrom, $priceTo);
    }
  }

  private static function setSortMode() {
    $sort = 'sale_rank';
    if (isset($_GET['sort'])) {
      $mapping = array(
        '价格' => 'lowest_price_x_100',
        '-价格' => 'lowest_price_x_100',
        '销量' => 'sale_rank',
        '上架时间' => 'publish_timestamp',
        '折扣' => 'discount_x_10'
      );
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

  private static function getResult() {
    if (!isset($GLOBALS['URI']['QUERY'])) {
      throw new NotFoundException;
    }
    $segmentList = Segmentation::execute($GLOBALS['URI']['QUERY']);
    $result = self::$sphinx->query($segmentList, 'wj_product_index');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}