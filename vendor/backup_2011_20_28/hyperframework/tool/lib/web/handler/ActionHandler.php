<?php
class ActionHandler {
  public function handle($class, $fullPath) {
    $postfix = 'Action';
    if (substr($class, -(strlen($postfix))) !== $postfix) {
      return;
    }
    return $this->getCache($class, $fullPath);
  }

  private function getCache($class, $fullPath) {
    $cache = array('class' => $class, 'method' => array ());
    $reflectors = $this->getMethodReflectors($class, $fullPath);
    foreach ($reflectors as $reflector) {
      $method = $reflector->getName();
      if (!preg_match('/^[A-Z]+$/', $method)) {
        throw new Exception("Invalid action method '$method' in '$fullPath'");
      }
      $cache['method'][] = $method;
    }
    return $cache;
  }

  private function getMethodReflectors($class, $fullPath) {
    $reflector = new ReflectionClass($class);
    return $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
  }
}