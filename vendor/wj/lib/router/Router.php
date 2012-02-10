<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
          'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
      return '/redirect';
    }
    $GLOBALS['CONTEXT'] = array('REQUEST_PATH' => $_SERVER['REQUEST_URI']);
  }
}