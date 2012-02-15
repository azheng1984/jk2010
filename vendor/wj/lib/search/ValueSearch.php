<?php
class ValueSearch {
  public static function search() {
    $handler = ProductSearchService::getHandler();
    if ($handler === false) {
      return false;
    }
    if (isset($GLOBALS['KEY']['mva_index']) === false) {
      return false;
    }
    $handler->SetGroupBy('value_id_list_'.$GLOBALS['KEY']['mva_index'],
      SPH_GROUPBY_ATTR, '@count DESC');
    return ProductSearchService::search($handler);
  }
}