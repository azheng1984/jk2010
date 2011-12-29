<?php
class KeySearch {
  public static function search() {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetFilter (
      'category_id', array($GLOBALS['URI']['CATEGORY']['id'])
    );
    $sphinx->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    self::setPage();
    $segmentList = Segmentation::execute($GLOBALS['URI']['QUERY']);
    $result = self::$sphinx->query(
      implode(' ', $segmentList), 'wj_product_index'
    );
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }

  private static function setPage() {
    $page = 1;
    if (isset($GLOBALS['URI']['PAGE'])) {
      $page = $GLOBALS['URI']['PAGE'];
    }
    $offset = ($page - 1) * 16;
    self::$sphinx->SetLimits($offset, 16);
  }
}