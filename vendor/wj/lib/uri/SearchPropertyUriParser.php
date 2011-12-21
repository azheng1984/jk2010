<?php
class SearchPropertyUriParser {
  private static $key = false;
  private static $keyUriName;
  private static $valueList;
  private static $activeList = array();
  private static $activeValueList;

  /* key=value&key=value&!value&key=!value */
  public static function parse() {
    $GLOBALS['URI']['PROPERTY_LIST'] = array();
    $blockList = explode('&', $GLOBALS['URI']['PATH_SECTION_LIST'][3]);
    foreach ($blockList as $block) {
      $list = explode('=', $block, 2);
      if (count($list) === 2) {
        self::moveNext(array_shift($list));
      }
      if (self::$key === false) {
        continue;
      }
      $valueUriName = $list[0];
      $isInclude = true;
      if (strpos($valueUriName, '!') === 0) {
        $isInclude = false;
        $valueUriName = substr($valueUriName, 1);
      }
      $value = DbProperty::getValueByName(
        self::$key['id'], urldecode($valueUriName)
      );
      if ($value !== false) {
        $value['is_include'] = $isInclude;
        self::$values[] = $value;
        self::$activeValueList[$value['id']] = $list[0];
      }
    }
    self::moveNext();
    if (count(self::$activeList) !== 0) {
      ksort(self::$activeList);
      $GLOBALS['URI']['STANDARD_PATH'] .= implode('&', self::$activeList);
    }
  }

  private static function moveNext($uriKeyName = null) {
    if (self::$key !== false && count(self::$valueList) !== 0) {
      $GLOBALS['URI']['PROPERTY_LIST'][] =
        array('KEY' => self::$key, 'VALUES' => self::$valueList);
      ksort(self::$activeValueList);
      self::$activeList[self::$key['id']] =
        self::$keyUriName.'='.implode('&', self::$activeValueList);
    }
    if ($uriKeyName !== null) {
      self::$key = DbProperty::getKeyByName(
        $GLOBALS['URI']['CATEGORY']['id'], urldecode($uriKeyName)
      );
      self::$keyUriName = $uriKeyName;
      self::$values = array();
      self::$activeValueList = array();
    }
  }
}