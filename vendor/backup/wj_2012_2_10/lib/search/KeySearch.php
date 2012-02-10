<?php
class KeySearch {
  public static function search() {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    $sphinx->SetFilter (
      'category_id', array($GLOBALS['URI']['CATEGORY']['id'])
    );
    $sphinx->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    self::setPage($sphinx);
    $query = SegmentationService::execute($GLOBALS['URI']['QUERY']);
    $result = $sphinx->query($query, 'wj_product'
    );
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }

  private static function setPage($sphinx) {
    $page = 1;
    if (isset($GLOBALS['URI']['PAGE'])) {
      $page = $GLOBALS['URI']['PAGE'];
    }
    $offset = ($page - 1) * 16;
    $sphinx->SetLimits($offset, 16);
  }
}