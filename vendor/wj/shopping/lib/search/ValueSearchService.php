<?php
class ValueSearchService {
  public static function search() {
    $handler = SearchService::getHandler(20);
    if ($handler === false) {
      return false;
    }
    if (isset($GLOBALS['KEY']['mva_index']) === false) {
      return false;
    }
    $handler->SetGroupBy('value_id_list_'.$GLOBALS['KEY']['mva_index'],
      SPH_GROUPBY_ATTR, '@count DESC');
    return SearchService::search($handler);
  }
}