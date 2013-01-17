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
    if (!isset($GLOBALS['PATH_SECTION_LIST'][2])) {
      return '/';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === 'edit') {
      $GLOBALS['NAVIGATION_MODULE'] = 'book';
      return '/edit';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === 'book') {
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'page') {
        $GLOBALS['NAVIGATION_MODULE'] = 'book';
        return '/book/page';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'discussion') {
        $GLOBALS['NAVIGATION_MODULE'] = 'discussion';
        if ($GLOBALS['PATH_SECTION_LIST'][4] === 'new') {
          return '/discussion/new';
        }
        if (is_numeric($GLOBALS['PATH_SECTION_LIST'][4])) {
          if ($GLOBALS['PATH_SECTION_LIST'][5] === 'new') {
            return '/topic/new';
          }
          return '/topic';
        }
        return '/discussion';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'task') {
        $GLOBALS['NAVIGATION_MODULE'] = 'task';
        return '/task';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'history') {
        $GLOBALS['NAVIGATION_MODULE'] = 'history';
        return '/history';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'download') {
        $GLOBALS['NAVIGATION_MODULE'] = 'download';
        return '/download';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'member') {
        $GLOBALS['NAVIGATION_MODULE'] = 'member';
        return 'member';
      }
      $GLOBALS['NAVIGATION_MODULE'] = 'book';
      return '/book';
    }
    return '/category';
  }
}