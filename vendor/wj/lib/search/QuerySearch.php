<?php
class QuerySearch {
  public static function searchByCategory($amount = 60) {
    
  }

  public static function searchByPropertyValue() {
    
  }

  public static function searchByQuery($query) {
    $segmentList = Segmentation::execute($query);
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    if (strpos($segmentList, ' ') !== false) {
      $amount = count(explode(' ', $segmentList)) - 1;
      $sphinx->SetMatchMode(SPH_MATCH_EXTENDED);
      $segmentList = '"'.$segmentList.'"/'.$amount;
    }
    $result = $sphinx->query($segmentList, 'wj_query_index');
    if ($result === false) {
      $result = array('total_found' => 0, 'matches' => array());
    }
    return $result;
  }
}