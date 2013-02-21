<?php
class Router {
  public static function execute() {
    session_start();
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
      return $path;
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === 'search') {
      return SearchRouter::execute();
    }
    $tmp = explode('-', $GLOBALS['PATH_SECTION_LIST'][1], 2);
    $id = null;
    if (count($tmp) === 2 && is_numeric($tmp[1]) === true) {
      $id = $tmp[1];
    }
    switch ($tmp[0]) {
      case 'article':
        return ArticleRouter::execute($id);
      case 'category':
        return CategoryRouter::execute($id);
      case 'user':
        return UserRouter::execute($id);
      default:
        echo 'xxx';
        return $path;
    }
  }
}