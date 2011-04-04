<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (isset($config['sub'])) {
      $explorer = new PackageExplorer;
      $explorer->render($config);
      return;
    }
    $this->execute($config, $arguments);
  }

  private function execute($config, $arguments) {
    if (!isset($config['class'])) {
      throw new CommandException('Class not defined');
    }
    $reflector = null;
    try {
      $reflector = new ReflectionMethod($config['class'], 'execute');
    } catch (ReflectionException $exception) {
      throw new CommandException($exception->getMessage());
    }
    $verifier = new ArgumentVerifier;
    $verifier->verify(
      $reflector, count($arguments), in_array('infinite', $config, true)
    );
    $reflector->invokeArgs(new $config['class'], $arguments);
  }
}