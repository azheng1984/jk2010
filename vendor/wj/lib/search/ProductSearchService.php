<?php
class ProductSearchService {
  public static function search() {
    $handler = SearchService::getHandler();
    if ($handler === false) {
      return false;
    }
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
    $handler->SetSortMode($mode, $mapping[$GLOBALS['SORT']]);
    return SearchService::search($handler);
  }
}