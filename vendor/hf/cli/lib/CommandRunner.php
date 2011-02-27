<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (!isset($config['class'])) {
      throw new SyntaxException;
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $length = count($arguments);
    $isInfiniteArgument = in_array('infinite_argument', $config, true);
    $verifier = new ArgumentVerifier;
    $verifier->verify($reflector, $length, $isInfiniteArgument);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }
}