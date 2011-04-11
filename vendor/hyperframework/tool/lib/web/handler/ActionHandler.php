<?php
class ActionHandler {
  public function execute($fileName, $fullPath) {
    $postfix = 'Action.php';
    if (substr($fileName, -(strlen($postfix))) !== $postfix) {
      return;
    }
    require $fullPath;
    $class = preg_replace('/.php$/', '', $fileName);
    $cache = array('class' => $class, 'method' => array ());
    $reflector = new ReflectionClass($class);
    foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
      $methodName = $method->getName();
      if (preg_match('/^[A-Z]+$/', $methodName)) {
        $cache['method'][] = $methodName;
      }
    }
    return $cache;
  }
}