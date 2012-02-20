<?php
class SearchPropertyListPathParser {
  private $key = null;
  private $valueList;

  /* key=value&key=value&value&key=value */
  public function parse() {
    if ($GLOBALS['PATH_SECTION_LIST'][3] === '') {
      throw new NotFoundException;
    }
    $GLOBALS['PROPERTY_LIST'] = array();
    foreach (explode('&', $GLOBALS['PATH_SECTION_LIST'][3]) as $section) {
      $list = explode('=', $section, 2);
      if (count($list) === 2) {
        $this->moveNextKey(array_shift($list));
      }
      if ($this->key === null) {
        throw new NotFoundException;
      }
      $this->addValue($list[0]);
    }
    $this->saveProperty();
  }

  private function moveNextKey($path) {
    if ($path === '') {
      throw new NotFoundException;
    }
    if ($this->key !== null) {
      $this->saveProperty();
    }
    $this->valueList = array();
    $keyName = urldecode($path);
    $this->key = false;
    if (isset($GLOBALS['CATEGORY']['id'])) {
      $this->key = DbPropertyKey::getByName(
        $GLOBALS['CATEGORY']['id'], $keyName
      );
    }
    if ($this->key === false) {
      $this->key = array('name' => $keyName);
    }
  }

  private function saveProperty() {
    if (count($this->valueList) === 0) {
      throw new NotFoundException;
    }
    $GLOBALS['PROPERTY_LIST'][$this->key['name']] = array(
      'KEY' => $this->key, 'VALUE_LIST' => $this->valueList
    );
  }

  private function addValue($path) {
    if ($path === '') {
      throw new NotFoundException;
    }
    $value = false;
    $valueName = urldecode($path);
    if (isset($this->key['id'])) {
      $value = DbPropertyValue::getByName($this->key['id'], $valueName);
    }
    if ($value === false) {
      $value = array('name' => $valueName);
    }
    $this->valueList[] = $value;
  }
}