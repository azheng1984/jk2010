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
    list($path, $parameters) = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($path !== '/') {
      throw new NotFoundException;
    }
    return '/search';
  }
}