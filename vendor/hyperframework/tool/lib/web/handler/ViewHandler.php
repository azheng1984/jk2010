<?php
class ViewHandler {
  private $types;

  public function __construct($types = array()) {
    if (!is_array($types)) {
      $types = array($types);
    }
    $this->verifyTypes($types);
    $this->types = $types;
  }

  public function handle($class, $fullPath) {
    foreach ($this->types as $type) {
      $length = strlen($type);
      if ($length === 0) {
        continue;
      }
      if (substr($class, -strlen($type)) === $type) {
        $this->verifyRenderingMethod($class, $fullPath);
        return array($type => $class);
      }
    }
  }

  private function verifyTypes($types) {
    foreach ($types as $type) {
      $pattern = '/^([A-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/';
      if (!preg_match($pattern, $type)) {
        throw new Exception("View type '$type' is invalid");
      }
    }
  }

  private function verifyRenderingMethod($class, $fullPath) {
    if (!class_exists($class)) {
      require $fullPath;
    }
    $reflector = new ReflectionClass($class);
    if (!$reflector->hasMethod('render')) {
      throw new Exception("Rendering method of view not found in '$fullPath'");
    }
    if (!$reflector->getMethod('render')->isPublic()) {
      throw new Exception("Rendering method of view not public in '$fullPath'");
    }
  }
}