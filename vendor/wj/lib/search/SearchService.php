<?php
class SearchService {
  public static function getHandler($itemsPerPage = 16) {
    $handler = new SphinxClient;
    $handler->SetServer("localhost", 9312);
    $handler->SetMaxQueryTime(1000);
    $handler->SetMatchMode(SPH_MATCH_BOOLEAN);
    if (self::setRecognition($handler) === false) {
      return false;
    }
    if (self::setCategory($handler) === false) {
      return false;
    }
    if (self::setPropertyList($handler) === false) {
      return false;
    }
    self::setPriceRange($handler);
    self::setPage($handler, $itemsPerPage);
    return $handler;
  }

  public function search($handler) {
    $query = SegmentationService::execute($GLOBALS['QUERY']['name']);
    return $handler->Query($query, 'wj_product');
  }

  private static function setRecognition($handler) {
    if (isset($GLOBALS['IS_RECOGNITION']) === false) {
      return;
    }
    if (isset($GLOBALS['QUERY']['id']) === false) {
      return false;
    }
    $handler->SetFilter('query_id', array($GLOBALS['QUERY']['id']));
  }

  private static function setCategory($handler) {
    if (isset($GLOBALS['CATEGORY']) === false) {
      return;
    }
    if (isset($GLOBALS['CATEGORY']['id']) === false) {
      return false;
    }
    $handler->SetFilter('category_id', array($GLOBALS['CATEGORY']['id']));
  }

  private static function setPropertyList($handler) {
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

  private static function setPriceRange($handler) {
    $max = 0;
    $min = 0;
    if (isset($GLOBALS['PRICE_FROM'])) {
      $max = $GLOBALS['PRICE_FROM'] * 100;
    }
    if (isset($GLOBALS['PRICE_TO'])) {
      $min = $GLOBALS['PRICE_TO'] * 100;
    }
    if ($max === 0 && $min === 0) {
      return;
    }
    if ($min > $max) {
      list($min, $max) = array($max, $min);
    }
    $handler->SetFilterRange('lowest_price_x_100', $min, $max);
  }

  private static function setPage($handler, $itemsPerPage) {
    $offset = ($GLOBALS['PAGE'] - 1) * $itemsPerPage;
    $handler->SetLimits($offset, $itemsPerPage);
  }
}