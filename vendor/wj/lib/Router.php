<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
          'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
      return '/redirect';
    }
    if ($_SERVER['SCRIPT_NAME'] === '/'
      && isset($_GET['q']) && $_GET['q'] !== '') {
      $this->redirectQuery();
      return '/redirect';
    }
    $GLOBALS['PATH_SECTION_LIST'] = explode('/', $_SERVER['SCRIPT_NAME']);
    if (!isset($GLOBALS['PATH_SECTION_LIST'][2])) {
      return '/';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][1] === '+i') {
      return '/index';
    }
    return '/search';
  }

  private function redirectQuery() {
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