<?php
class Router {
  public function getPath() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $path = null;
    if ($requestUri == '/') {
      $path = 'home';
    } elseif ($this->endsWith($requestUri, '/')) {
      $path = 'category';
    } else {
      $path = 'document';
    }
    return $path;
  }

  private function endsWith($haystack, $needle) {
    return strrpos($haystack, $needle) === strlen($haystack) - strlen($needle);
  }
}