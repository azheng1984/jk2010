<?php
class ValueSearch {
  private static $sphinx;

  public static function search() {
    $sphinx = new SphinxClient;
    self::$sphinx = $sphinx;
    self::setPropertyList();
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $key = $GLOBALS['URI']['PROPERTY_LIST'][0];
    $sphinx->SetGroupBy(
      'value_id_list_'.$key['mva_index'], SPH_GROUPBY_ATTR, '@count DESC'
    );
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

  private static function setPropertyList() {
    foreach ($GLOBALS['URI']['PROPERTY_LIST'] as $property) {
      self::$sphinx->SetFilter(
        'value_id_list_'.$property['KEY']['mva_index'],
        $this->getValueIdList($property['VALUE_LIST'])
      );
    }
  }

  private static function getValueIdList($valueList) {
    $result = array();
    foreach ($valueList as $value) {
      $result[] = $value['id'];
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