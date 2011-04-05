<?php
class CommandRunner {
  public function run($config, $options, $arguments) {
    if (isset($config['sub'])) {
      ExplorerContext::getExplorer('Package')->render($config);
      return;
    }
    $this->execute($config, $options, $arguments);
  }

  private function execute($config, $options, $arguments) {
    if (!isset($config['class'])) {
      throw new CommandException('Class is not defined');
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
    $reflector->invokeArgs(new $config['class']($options), $arguments);
  }
}