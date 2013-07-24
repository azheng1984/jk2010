<?php
class ApplicationConfiguration {
  public function extract($config) {
    if (!is_array($config)) {
      $config = array($config);
    }
    $handlers = array();
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        list($key, $value) = array($value, null);
      }
      $handlers[$key] = $this->getHandler($key, $value);
    }
    return $handlers;
  }

  private function getHandler($name, $config) {
    $class = $name.'Handler';
    $isConfigAcceptable = $this->isConfigAcceptable($class);
    if ($isConfigAcceptable === false && $config !== null) {
      throw new Exception("Application handler '$name' do not accept config");
    }
    if ($isConfigAcceptable === true && $config === null) {
      throw new Exception("Application handler '$name' must contain config");
    }
    if ($config === null) {
      return new $class;
    }
    return new $class($config);
  }

  private function isConfigAcceptable($class) {
    $reflector = new ReflectionClass($class);
    $constructor = $reflector->getConstructor();
    if ($constructor === null) {
      return false;
    }
    $parameters = $constructor->getParameters();
    if (count($parameters) === 0) {
      return false;
    }
    if ($parameters[0]->isOptional()) {
      return 'optional';
    }
    return true;
  }
}