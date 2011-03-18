<?php
class MethodView {
  public function render($name, $config) {
    $writter = $_ENV['command_writer'];
    if (!isset($config['class'])) {
      $writter->writeLine($name);
      return;
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $arguments = $reflector->getParameters();
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