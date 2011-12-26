<?php
class CategorySearch {
  public static function search($query) {
    $sphinx = new SphinxClient;
//    $offset = ($this->page - 1) * 20;
//    $sphinx->SetLimits($offset, 20);
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetGroupBy('category_id', SPH_GROUPBY_ATTR, '@count DESC');
    return $sphinx->query($query, 'wj_product_index');
  }
}