<?php
class ActionBuilder {
  public function build($fileName, $path) {
    $postfix = 'Action.php';
    if (substr($fileName, -(strlen($postfix))) !== $postfix) {
      return;
    }
    require $path;
    $class = preg_replace('/.php$/', '', $fileName);
    $cache = array ('class' => $class, 'method' => array ());
    $reflector = new ReflectionClass($class);
    $methods = array();
    foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
      if (preg_match('/^[A-Z]+$/', $method)) {
        $cache['method'][] = $method->getName();
      }
    }
    return $cache;
  }
}