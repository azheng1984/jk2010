<?php
class Router
{
  public static function getPath() {
    $requestUri = $_SERVER['REQUEST_URI'];
    $app = null;
    if ($requestUri == '/') {
      $app = 'home';
    } elseif (self::endsWith($requestUri, '/')) {
      $app = 'category';
    } else {
      $app = 'document';
    }
    return $app;
  }

  private static function endsWith($haystack, $needle) {
    return strrpos($haystack, $needle) === strlen($haystack)-strlen($needle);
  }
}