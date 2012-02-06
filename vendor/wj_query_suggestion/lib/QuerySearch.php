<?php
class QuerySearch {
  public static function search() {
    $query = urldecode(substr($_SERVER['REQUEST_URI'], '1'));
    if ($query === '') {
      return array();
    }
    $query = SegmentationService::execute($query);
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    $sphinx->SetSortMode(SPH_SORT_ATTR_DESC, 'product_amount');
    $result = $sphinx->query($query, 'wj_query');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}