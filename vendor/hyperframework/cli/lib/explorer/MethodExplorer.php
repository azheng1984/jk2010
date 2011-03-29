<?php
class MethodExplorer {
  public function render($prefix, $method, $config, $writer) {
    if (!isset($config['class'])) {
      $writer->writeLine($prefix);
      return;
    }
    $reflector = new ReflectionClass($config['class']);
    if (!$reflector->hasMethod($method)) {
      $writer->writeLine($prefix);
      return;
    }
    $arguments = $reflector->getMethod($method)->getParameters();
    $isInfinite = isset($config['infinite']);
    $output = $prefix;
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