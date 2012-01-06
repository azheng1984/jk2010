<?php
class QuerySearch {
  public static function search() {
    $segmentList = Segmentation::execute($GLOBALS['URI']['QUERY']);
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $query = implode(' ', $segmentList);
    if (count($segmentList) !== 1) {
      $amount = count($segmentList) - 1;
      $sphinx->SetMatchMode(SPH_MATCH_EXTENDED);
      $query = '"'.$query.'"/'.$amount;
    }
    $result = $sphinx->query($query, 'wj_query_index');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}