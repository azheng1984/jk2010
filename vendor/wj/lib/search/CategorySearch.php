<?php
class CategorySearch {
  public static function search() {
    $handler = ProductSearchService::getHandler();
    if ($handler === false) {
      return false;
    }
    $handler->SetGroupBy('category_id', SPH_GROUPBY_ATTR, '@count DESC');
    return SearchService::search($handler, $GLOBALS['QUERY']['name']);
  }
}