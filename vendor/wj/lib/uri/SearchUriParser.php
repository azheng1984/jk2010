<?php
class SearchUriParser {
  public static function parse($sections) {
    array_shift($sections);
    array_pop($sections);
    $amount = count($sections);
    if ($amount === 0 || $sections[0] === '') {
      throw new NotFoundException;
    }
    $GLOBALS['URI'] = array();
    $GLOBALS['URI']['QUERY'] = urldecode($sections[0]);
    $result = '/'.$sections[0].'/';
    if ($amount > 1) {
      $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
        urldecode($sections[1])
      );
      $result .= $sections[1].'/';
    }
    if ($amount > 2) {
      $GLOBALS['URI']['PROPERTIES'] = $this->parseProperties($sections[2]);
      $result .= $sections[2].'/';
    }
    $arguments = array();
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $arguments[] = 'id='.$_GET['id'];
    }
    if (isset($_GET['sort'])) {
      $arguments[] = 'sort='.urlencode($_GET['sort']);
    }
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $arguments[] = 'page='.$_GET['page'];
    }
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
      $arguments[] = 'media=json';
    }
    if (count($arguments) > 0) {
      $result .= '?'.implode('&', $arguments);
    }
    return $result;
  }

  private function parseProperties($section) {
    $properties = array();
    $key = false;
    $values = null;
    $items = explode('&', urldecode());
    foreach ($items as $item) {
      $tmps = explode('=', $item, 2);
      if (count($tmps) === 2) {
        if ($key !== false) {
          $properties[] = array('key' => $key, 'values' => $values);
        }
        $key = DbProperty::getKeyByName(
          $GLOBALS['URI']['CATEGORY']['id'], array_shift($tmps)
        );
        $values= array();
      }
      $values[] = DbProperty::getValueByName($key, $tmps[0]);
    }
    return $properties;
  }
}