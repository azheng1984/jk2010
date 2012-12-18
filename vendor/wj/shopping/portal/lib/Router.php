<?php
class Router {
  public static function execute() {
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
        'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
      return '/redirect';
    }
    if ($_SERVER['SCRIPT_NAME'] === '/'
        && isset($_GET['q']) && $_GET['q'] !== '') {
      self::redirectQuery();
      return '/redirect';
    }
    $path = $_SERVER['REQUEST_URI'];
    $queryStringPosition = strpos($_SERVER['REQUEST_URI'], '?');
    if ($queryStringPosition !== false) {
      $path = substr($_SERVER['REQUEST_URI'], 0, $queryStringPosition);
    }
    $GLOBALS['PATH'] = $path;
    $GLOBALS['PATH_SECTION_LIST'] = explode('/', $path);
    if (isset($_GET['media']) && $_GET['media'] === 'json') {
      $_SERVER['REQUEST_MEDIA_TYPE'] = 'Json';
    }
    if (!isset($GLOBALS['PATH_SECTION_LIST'][2])) {
      return '/';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === '+top') {
      return '/index';
    }
    return '/search';
  }

  private static function redirectQuery() {
    $query = urlencode(trim($_GET['q']));
    if ($query === '%2B') {
      $query = '';
    }
    if ($query !== '') {
      $query .= '/';
    }
    header('Location: /'.$query);
  }
}