<?php
class ProductSearch {
  public static function search() {
    $handler = ProductSearchService::getHandler();
    if ($handler === false) {
      return false;
    }
    self::setSort();
    return ProductSearchService::search($handler);
  }

  private static function setSort($handler) {
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
  }
}