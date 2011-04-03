<?php
class MethodExplorer {
  private $writer;

  public function __construct($writer) {
    $this->writer = $writer;
  }

  public function render($name, $method, $config) {
    if (!isset($config['class'])) {
      $this->writer->writeLine($name);
      return;
    }
    $reflector = new ReflectionClass($config['class']);
    if (!$reflector->hasMethod($method)) {
      $this->writer->writeLine($name);
      return;
    }
    $arguments = $reflector->getMethod($method)->getParameters();
    $isInfinite = isset($config['infinite']);
    $output = $name;
    if (count($arguments) !== 0 || $isInfinite) {
      $output .= '('.$this->getArgumentList($arguments, $isInfinite).')';
    }
    $this->writer->writeLine($output);
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