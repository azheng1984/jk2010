<?php
class CategorySearchService {
  public static function search() {
    $handler = SearchService::getHandler(20);
    if ($handler === false) {
      return false;
    }
    $handler->SetGroupBy('category_id', SPH_GROUPBY_ATTR, '@count DESC');
    return SearchService::search($handler);
  }
}