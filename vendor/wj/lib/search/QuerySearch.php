<?php
class QuerySearch {
  public static function search() {
    $query = SegmentationService::execute($GLOBALS['URI']['QUERY']);
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    $result = $sphinx->query($query, 'wj_query');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}