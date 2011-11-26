<?php
class ProductSearch {
  public static function search() {
    $page = 1;
    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    }
    $sort = 'sale_rank';
    if (isset($_GET['sort'])) {
      if ($_GET['sort'] === '价格') {
        $sort = 'lowest_price_x_100';
      }
    }
    $sphinx = new SphinxClient;
    $offset = ($page - 1) * 16;
    $sphinx->SetLimits($offset, 16);
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetSortMode(SPH_SORT_ATTR_ASC, $sort);
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      $sphinx->SetFilter (
        'category_id', array($GLOBALS['URI']['CATEGORY']['id'])
      );
    }
    /*
    if ($properties !== null && count($properties) !== 0) {
      foreach ($properties as $item) {
        $sphinx->SetFilter(
          'value_id_list_'.$item['key']['mva_index'],
          array($item['value']['id'])
        );
      }
    }
    */
    $result = $sphinx->query($GLOBALS['URI']['QUERY'], 'wj_search');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}