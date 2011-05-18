<?php
class Router {
  public function execute() {
    $pathBuilder = new PathBuilder;
    $sections = explode('?', $_SERVER['REQUEST_URI'], 2);
    if ($sections[0] === '/') {
      return '/home';
    }
    $path = $pathBuilder->execute(explode('/', $sections[0]));
    if (isset($_GET['_path'])) {
      $path = $this->rewritePath($path);
    }
    return $path;
  }

  private function getPath($path) {
    if (substr($_GET['_path'], 0, 1) === '/') {
      return $_GET['_path'];
    }
    return $path.'/'.$_GET['_path'];
  }
}