<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] === 'www.'.DOMAIN_NAME) {
      header('Location: http://'.DOMAIN_NAME.$_SERVER['REQUEST_URI']);
      return '/redirect';
    }
    if ($_SERVER['REQUEST_URI'] === '/') {
      return '/';
    }
    list($path) = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($path !== '/') {
      return $this->parsePath($path);
    }
    if ($_GET['q'] !== '') {
      header('Location: /');
      return '/redirect';
    }
    throw new NotFoundException;
  }

  private function parsePath($path) {
    $sections = explode('/', $path);
    if (count($sections) === 3 && $sections[1] === 'r') {
      return '/product';
    }
    return '/search';
  }
}
