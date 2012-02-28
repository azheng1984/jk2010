<?php
class QuerySearch {
  public static function search($query) {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    $sphinx->SetSortMode(SPH_SORT_ATTR_DESC, 'product_amount');
    return $sphinx->query($query, 'wj_query');
  }
}