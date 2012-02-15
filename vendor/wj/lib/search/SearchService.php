<?php
class SearchService {
  public static function getHandler() {
    $sphinx = new SphinxClient;
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(1000);
    return $sphinx;
  }

  public static function search($handler, $query, $index = 'wj_product') {
    $query = SegmentationService::execute($query);
    return $handler->query($query, $index);
  }
}