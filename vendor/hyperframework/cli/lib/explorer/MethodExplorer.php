<?php
class MethodExplorer {
  public function render($name, $method, $config) {
    $writter = $_ENV['command_writer'];
    if (!isset($config['class'])) {
      $writter->writeLine($name);
      return;
    }
    $reflector = new ReflectionClass($config['class']);
    if (!$reflector->hasMethod($method)) {
      return;
    }
    $arguments = $reflector->getMethod($method)->getParameters();
    $output = $name;
    if (count($arguments) !== 0) {
      $output .= '('.$this->getArgumentList($arguments).')';
    }
    $writter->writeLine($output);
  }

  private function getArgumentList($arguments) {
    $outputs = array();
    foreach ($arguments as $argument) {
      $item = $argument->getName();
      if ($argument->isOptional()) {
        $item .= ' = '.var_export($argument->getDefaultValue(), true);
      }
      $outputs[] = $item;
    }
    return implode(', ', $outputs);
  }
}