<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] === 'www.wj.com') {
      header('Location: http://wj.com'.$_SERVER['REQUEST_URI']);
      return '/redirect';
    }
    if ($_SERVER['REQUEST_URI'] === '/') {
      return '/';
    }
    $tmps = explode('?', $_SERVER['REQUEST_URI'], 2);
    $sections = explode('/', $tmps[0]);
    if (count($sections) === 2 && $sections[1] !== '') {
      $_GET['product_id'] = $sections[1];
      return '/product';
    }
    $_GET['categories'] = $sections;
    return '/search';
  }
}