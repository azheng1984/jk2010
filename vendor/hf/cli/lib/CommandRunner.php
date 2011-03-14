<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (!isset($config['class'])) {
      $index = new CommandIndex;
      $index->render($config);
      return;
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($arguments);
    $isInfiniteArgument = in_array('infinite_argument', $config, true);
    $verifier->verify($reflector, $length, $isInfiniteArgument);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }
}