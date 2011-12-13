<?php
class ValueSearch {
  public static function search($query, $category, $key) {
    $sphinx = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$sphinx->SetLimits($offset, 20);
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetFilter('category_id', array($category['id']));
    $sphinx->SetFilter('key_id_list', array($key['id']));
    $sphinx->SetGroupBy('value_id_list_'.$key['mva_index'], SPH_GROUPBY_ATTR, '@count DESC');
    //$sphinx->SetGroupBy('value_id_list'.$this->key['search_field_index'], SPH_GROUPBY_ATTR, '@count DESC');
    $sphinx->SetArrayResult (true);
    return $sphinx->query($query, 'wj_search');
  }
}