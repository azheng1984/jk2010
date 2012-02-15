<?php
class SearchService {
  public static function getHandler() {
    $handler = new SphinxClient;
    $handler->setServer("localhost", 9312);
    $handler->setMaxQueryTime(1000);
    return $handler;
  }

  public static function search($handler, $query, $index) {
    $query = SegmentationService::execute($query);
    return $handler->query($query, $index);
  }
}