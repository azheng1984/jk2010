<?php
class FilterParameter {
  public static function getSelectedList($category) {
    $result = array();
    foreach ($_GET as $keyName => $valueName) {
      $key = DbProperty::getKeyByName($category['table_prefix'], $keyName);
      if ($key === false) {
        continue;
      }
      $valueNames = preg_split('/(?<! ):(?<! )/', $valueName);
      $values = array();
      foreach ($valueNames as $item) {
        $value = DbProperty::getValueByKeyIdAndName($category['table_prefix'], $key['id'], $item);
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