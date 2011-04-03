<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (isset($config['sub'])) {
      $explorer = new PackageExplorer;
      $explorer->render($config);
      return;
    }
    if (!isset($config['class'])) {
      throw new CommandException('Class not defined');
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $length = count($arguments);
    $isInfinite = in_array('infinite', $config, true);
    $verifier = new ArgumentVerifier;
    $verifier->verify($reflector, $length, $isInfinite);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }
}