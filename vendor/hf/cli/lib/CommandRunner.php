<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (!isset($config['class'])) {
      $this->printList($config);
      return;
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($arguments);
    $isInfiniteArgument = in_array('infinite_argument', $config, true);
    $verifier->verify($reflector, $length, $isInfiniteArgument);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }

  private function printList($config) {
    if (isset($config['sub'])) {
      foreach ($config['sub'] as $name => $item) {
        echo $name."\n";
      }
    }
  }
}