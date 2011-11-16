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
    if ($_SERVER['HTTP_HOST'] === 'img.wj.com') {
      return '/image';
    }
    list($path) = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($path !== '/') {
      return $this->parsePath($path);
    }
    if ($_GET['q'] === '') {
      header('Location: /');
      return '/redirect';
    }
    return '/search';
  }

  private function parsePath($path) {
    $sections = explode('/', $path);
    if (count($sections) === 3 && $sections[1] === 'r') {
      return '/product';
    }
    throw new NotFoundException;
  }
}