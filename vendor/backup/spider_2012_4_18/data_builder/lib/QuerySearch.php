<?php
class QuerySearch {
  public static function search($precision = 1) {
    $query = SegmentationService::execute($GLOBALS['URI']['QUERY']);
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $segmentList = explode(' ', $query);
    $amount = count($segmentList);
    if ($precision !== 1 && $amount !== 1) {
      $quorum = round($amount * $precision);
      $sphinx->SetMatchMode(SPH_MATCH_EXTENDED);
      $query = '"'.$query.'"/'.$quorum;
    }
    $result = $sphinx->query($query, 'wj_query');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}