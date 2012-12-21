<?php
class ProductSearchService {
  public static function search() {
    $handler = SearchService::getHandler();
    if ($handler === false) {
      return false;
    }
    self::setSort($handler);
    return SearchService::search($handler);
  }

  private static function setSort($handler) {
    if ($GLOBALS['SORT'] === 'popularity_rank') {
      $handler->SetSortMode(SPH_SORT_ATTR_DESC, 'popularity_rank');
      return;
    }
    $mapping = array(
      'price' => 'lowest_price_x_100 ASC',
      '-price' => 'lowest_price_x_100 DESC',
    );
    $handler->SetSortMode(
      SPH_SORT_EXTENDED, $mapping[$GLOBALS['SORT']].', popularity_rank DESC'
    );
  }
}