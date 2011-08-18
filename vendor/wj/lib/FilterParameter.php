<?php
class FilterParameter {
  public static function getSelectedList() {
    $result = array();
    foreach ($_GET as $keyName => $valueName) {
      $key = DbProperty::getKeyByName($keyName);
      if ($key === false) {
        continue;
      }
      $valueNames = preg_split('/(?<! ):(?<! )/', $valueName);
      $values = array();
      foreach ($valueNames as $item) {
        $value = DbProperty::getValueByKeyIdAndName($key['id'], $item);
        if ($value === false) {
          continue;
        }
        $values[] = $value;
      }
      if (count($values) > 0) {
        $result[] = array($key, $values);
      }
    }
    return $result;
  }
}