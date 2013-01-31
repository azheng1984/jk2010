<?php
class Router {
  public static function execute() {
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
        'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
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
    $depth = count($GLOBALS['PATH_SECTION_LIST']);
    if ($depth === 2) {
      return '/';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === 'search') {
      return SearchRouter::execute();
    }
    $tmp = explode('-', $GLOBALS['PATH_SECTION_LIST'][1], 2);
    if (count($tmp) !== 2 || is_numeric($tmp[1]) === false) {
      throw new Exception;
    }
    try {
      switch ($tmp[0]) {
        case 'article':
          return ArticleRouter::execute($tmp[1]);
        case 'category':
          return CategoryRouter::execute($tmp[1]);
        case 'user':
          return UserRouter::execute($tmp[1]);
      }
    } catch (Exception $excption) {}
    //not found
  }
}