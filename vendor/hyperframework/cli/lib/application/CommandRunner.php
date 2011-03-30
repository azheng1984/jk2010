<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (isset($config['sub'])) {
      $explorer = new PackageExplorer;
      return $explorer->render($config, new CommandWriter);
    }
    if (!isset($config['class'])) {
      throw new CommandException('command class not found');
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $length = count($arguments);
    $isInfinite = in_array('infinite', $config, true);
    $verifier = new ArgumentVerifier;
    $verifier->verify($reflector, $length, $isInfinite);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }
}