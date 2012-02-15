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

  public static function setRecognition($handler) {
    if (isset($GLOBALS['IS_RECOGNITION']) === false) {
      return;
    }
    if (isset($GLOBALS['QUERY']['id']) === false) {
      return false;
    }
    $handler->SetFilter('query_id', array($GLOBALS['QUERY']['id']));
  }

  public static function setCategory($handler) {
    if (isset($GLOBALS['CATEGORY']) === false) {
      return;
    }
    if (isset($GLOBALS['CATEGORY']['id']) === false) {
      return false;
    }
    $handler->SetFilter('category_id', array($GLOBALS['CATEGORY']['id']));
  }

  public static function setPropertyList($handler) {
    if (isset($GLOBALS['PROPERTY_LIST']) === false) {
      return;
    }
    foreach ($GLOBALS['PROPERTY_LIST'] as $property) {
      if (isset($property['KEY']['mva_index']) === false) {
        return false;
      }
      $valueIdList = self::getValueIdList($property['VALUE_LIST']);
      if ($valueIdList === false) {
        return false;
      }
      $handler->SetFilter(
        'value_id_list_'.$property['KEY']['mva_index'], $valueIdList
      );
    }
  }

  private static function getValueIdList($valueList) {
    $result = array();
    foreach ($valueList as $value) {
      if (isset($value['id']) === false) {
        return false;
      }
      $result[] = $value['id'];
    }
    return $result;
  }
}