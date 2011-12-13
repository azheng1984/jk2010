<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] === 'www.'.DOMAIN_NAME) {
      return $this->redirect('http://'.DOMAIN_NAME.$_SERVER['REQUEST_URI']);
    }
    if ($_SERVER['REQUEST_URI'] === '/') {
      return '/';
    }
    if (isset($_GET['q'])) {
      $location = $_GET['q'] === '' ? '' : $_GET['q'].'/';
      return $this->redirect('/'.$location);
    }
    list($path) = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($path === '/') {
      return $this->redirect('/');
    }
    $sections = explode('/', $path);
    if (substr($sections[1], 0, 1) === '+') {
      return $this->checkStandardUri(IndexUriParser::parse($sections));
    }
    if (count($sections) === 3 && $sections[1] === 'r'
      && is_numeric($sections[2])) {
      $GLOBALS['PRODUCT_ID'] = $sections[2];
      return '/product';
    }
    SearchUriParser::parse($sections);
    $this->checkStandardUri('/search');
  }

  private function redirect($uri) {
    header('Location: '.$uri);
    return '/redirect';
  }

  private function checkStandardUri($app) {
    if ($GLOBALS['URI']['STANDARD'] !== $_SERVER['REQUEST_URI']) {
      return $this->redirect($GLOBALS['URI']['STANDARD']);
    }
    return $app;
  }
}