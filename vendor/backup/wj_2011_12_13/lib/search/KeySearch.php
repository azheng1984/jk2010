<?php
class KeySearch {
  public static function search($query, $category) {
    $sphinx = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$sphinx->SetLimits($offset, 20);
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetFilter ('category_id', array($category['id']));
    $sphinx->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    $sphinx->SetArrayResult (true);
    return $sphinx->query($query, 'wj_search');
  }
}