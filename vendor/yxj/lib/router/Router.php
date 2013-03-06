<?php
class Router {
  public static function execute() {
    session_start();
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
        'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
      header('HTTP/1.1 301 Moved Permanently');
      return;
    }
    $path = $_SERVER['REQUEST_URI'];
    $queryStringPosition = strpos($_SERVER['REQUEST_URI'], '?');
    if ($queryStringPosition !== false) {
      $path = substr($_SERVER['REQUEST_URI'], 0, $queryStringPosition);
    }
    $GLOBALS['PATH'] = $path;
    if ($path === '/') {
      $GLOBALS['PATH_SECTION_LIST'] = array('', '');
      return '/';
    }
    $sectionList = explode('/', $path);
    $GLOBALS['PATH_SECTION_LIST'] = $sectionList;
    $result = '';
    $amount = count($sectionList);
    for ($index = 1; $index < $amount; ++$index) {
      $section = $sectionList[$index];
      if ($section === '') {
        unset($GLOBALS['PATH_SECTION_LIST'][$index]);
        break;
      }
      if (ctype_digit($section)) {
        break;
      }
      $dashPosition = strpos($section, '-');
      if ($dashPosition === false) {
        $result .= '/'.$section;
        continue;
      }
      $tmp = substr($section, 0, $dashPosition);
      $GLOBALS['PATH_SECTION_LIST'][$index] = array(
        $tmp, substr($section, $dashPosition + 1)
      );
      $result .= '/'.$tmp;
    }
    return $result;
  }
}