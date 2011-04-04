<?php
class MethodExplorer {
  public function render($name, $method, $config) {
    $writer = ExplorerContext::getWriter();
    if (!isset($config['class'])) {
      $writer->writeLine($name);
      return;
    }
    $reflector = new ReflectionClass($config['class']);
    if (!$reflector->hasMethod($method)) {
      $writer->writeLine($name);
      return;
    }
    $arguments = $reflector->getMethod($method)->getParameters();
    $isInfinite = isset($config['infinite']);
    $output = $name;
    if (count($arguments) !== 0 || $isInfinite) {
      $output .= '('.$this->getArgumentList($arguments, $isInfinite).')';
    }
    $writer->writeLine($output);
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