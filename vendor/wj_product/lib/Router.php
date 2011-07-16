<?php
class Router {
  public function execute() {
    if ($_SERVER['REQUEST_URI'] === '/') {
      return '/';
    }
    if (count(explode('/', $_SERVER['REQUEST_URI'], 3)) > 2) {
      return '/search';
    }
    return '/product';
  }
}