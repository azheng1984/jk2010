<?php
class ActionProcessorCacheBuilder {
  public function build($dirPath, $entry, &$pathCache) {
    $suffix = substr($entry, -10);
    if ($suffix === 'Action.php') {
      require $dirPath . '/' . $entry;
      $class = preg_replace('/.php$/', '', $entry);
      $actionCache = array ('class' => $class, 'method' => array ());
      $reflector = new ReflectionClass($class);
      $methods = array();
      foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        $actionCache['method'][] = $method->name;
      }
      $pathCache['action'] = $actionCache;
    }
  }
}