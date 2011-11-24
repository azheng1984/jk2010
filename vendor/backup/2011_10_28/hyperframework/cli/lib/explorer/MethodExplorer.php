<?php
class MethodExplorer {
  public function render($name, $method, $config) {
    $writer = ExplorerContext::getWriter();
    $method = $this->getReflectionMethod($method, $config);
    if ($method === null) {
      $writer->writeLine($name);
      return;
    }
    $arguments = $method->getParameters();
    $isInfinite = in_array('infinite', $config);
    $output = $name;
    if (count($arguments) !== 0 || $isInfinite) {
      $output .= '('.$this->getArgumentList($arguments, $isInfinite).')';
    }
    $writer->writeLine($output);
  }

  private function getReflectionMethod($method, $config) {
    if (!isset($config['class'])) {
      return;
    }
    try {
      $reflector = new ReflectionClass($config['class']);
    } catch (ReflectionException $exception) {
      return;
    }
    if (!$reflector->hasMethod($method)) {
      return;
    }
    return $reflector->getMethod($method);
  }

  private function getArgumentList($arguments, $isInfinite) {
    $outputs = array();
    foreach ($arguments as $argument) {
      $item = $argument->getName();
      if ($argument->isOptional()) {
        $item .= ' = '.var_export($argument->getDefaultValue(), true);
      }
      $outputs[] = $item;
    }
    if ($isInfinite) {
      $outputs[] = '...';
    }
    return implode(', ', $outputs);
  }
}