<?php
class ActionHandler {
  public function handle($fileName, $fullPath) {
    $postfix = 'Action.php';
    if (substr($fileName, -(strlen($postfix))) !== $postfix) {
      return;
    }
    return $this->getCache(preg_replace('/.php$/', '', $fileName), $fullPath);
  }

  private function getCache($class, $fullPath) {
    $cache = array('class' => $class, 'method' => array ());
    $reflectors = $this->getMethodReflectors($class, $fullPath);
    foreach ($reflectors as $reflector) {
      $method = $reflector->getName();
      if (!preg_match('/^[A-Z]+$/', $method)) {
        throw new Exception("Invalid action method '$method' in $fullPath");
      }
      $cache['method'][] = $method;
    }
    return $cache;
  }

  private function getMethodReflectors($class, $fullPath) {
    if (!class_exists($class)) {
      require $fullPath;
    }
    $reflector = new ReflectionClass($class);
    return $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
  }
}