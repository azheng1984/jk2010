<?php
class KeySearchService {
  public static function search() {
    $handler = SearchService::getHandler(20);
    if ($handler === false) {
      return false;
    }
    $handler->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    return SearchService::search($handler);
  }
}