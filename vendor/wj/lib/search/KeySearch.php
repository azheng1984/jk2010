<?php
class KeySearch {
  public static function search() {
    $handler = ProductSearchService::getHandler();
    if ($handler === false) {
      return false;
    }
     $handler->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    return SearchService::search($handler);
  }
}