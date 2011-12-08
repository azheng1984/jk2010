<?php
class SearchUriParser {
  public static function parse($sections) {
    array_shift($sections);
    array_pop($sections);
    $amount = count($sections);
    if ($amount === 0 || $sections[0] === '') {
      throw new NotFoundException;
    }
    $GLOBALS['URI']= array('QUERY' => urldecode($sections[0]));
    $path = '/'.$sections[0].'/';
    if ($amount > 1) {
      $GLOBALS['URI']['CATEGORY'] = DbCategory::getByName(
        urldecode($sections[1])
      );
      $path .= $sections[1].'/';
    }
    if ($amount > 2) {
      $GLOBALS['URI']['PROPERTIES'] = self::parseProperties($sections[2]);
      $path .= $sections[2].'/';
    }
    $GLOBALS['URI']['PATH'] = $path;
    $arguments = array();
    if (isset($_GET['key'])) {
      $arguments[] = 'key='.urlencode($_GET['key']);
      $GLOBALS['URI']['KEY'] = $_GET['key'];
    }
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $arguments[] = 'media=json';
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
      self::buildUri($arguments);
      return;
    }
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $arguments[] = 'id='.$_GET['id'];
    }
    if (isset($_GET['price'])) {
      $GLOBALS['URI']['PRICE'] = $this->parsePrice();
    }
    if (isset($_GET['sort'])) {
      $arguments[] = 'sort='.urlencode($_GET['sort']);
      $GLOBALS['URI']['SORT'] = $_GET['sort'];
    }
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $arguments[] = 'page='.$_GET['page'];
      $GLOBALS['URI']['PAGE'] = $_GET['sort'];
    }
    self::buildUri($arguments);
  }

  private static function parseProperties($section) {
    $properties = array();
    $key = false;
    $values = null;
    $items = explode('&', urldecode($section));
    foreach ($items as $item) {
      $tmps = explode('=', $item, 2);
      if (count($tmps) === 2) {
        if ($key !== false) {
          $properties[] = array('KEY' => $key, 'VALUES' => $values);
        }
        $key = DbProperty::getKeyByName(
          $GLOBALS['URI']['CATEGORY']['id'], array_shift($tmps)
        );
        $values= array();
      }
      $values[] = DbProperty::getValueByName($key['id'], $tmps[0]);
    }
    if ($key !== false) {
      $properties[] = array('KEY' => $key, 'VALUES' => $values);
    }
    return $properties;
  }

  private static function parsePrice() {
    
  }

  private static function buildUri($arguments) {
    $GLOBALS['URI']['ARGUMENTS'] = $arguments;
    $uri = $GLOBALS['URI']['PATH'];
    if (count($arguments) > 0) {
      $uri .= '?'.implode('&', $arguments);
    }
    $GLOBALS['URI']['STANDARD'] = $uri;
  }
}