<?php
class FilterParameter {
  public static function getSelectedList() {
    $result = array();
    foreach ($_GET as $keyName => $valueName) {
      $key = DbProperty::getKeyByName($keyName);
      if ($key === false) {
        continue;
      }
      $value = DbProperty::getValueByKeyIdAndName($key['id'], $valueName);
      if ($value === null) {
        continue;
      }
      $result []= array($key, $value);
    }
    return $result;
  }
}