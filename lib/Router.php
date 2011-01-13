<?php
class Router {
  public static function getPath() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $path = null;
    if ($requestUri == '/') {
      $path = 'home';
    } elseif (self::endsWith($requestUri, '/')) {
      $path = 'category';
    } else {
      $path = 'document';
    }
    return $path;
  }

  private static function endsWith($haystack, $needle) {
    return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
  }
}
