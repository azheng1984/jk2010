<?php
class CommandRunner {
  public function run($config, $options, $arguments) {
    if (isset($config['sub'])) {
      ExplorerContext::getExplorer('Package')->render($config);
      return;
    }
    $reflector = $this->getReflectionMethod($config);
    $verifier = new ArgumentVerifier;
    $verifier->verify(
      $reflector, count($arguments), in_array('infinite', $config, true)
    );
    $reflector->invokeArgs(new $config['class']($options), $arguments);
  }

  private function getReflectionMethod($config) {
    if (!isset($config['class'])) {
      throw new CommandException('Command class not defined');
    }
    try {
      return new ReflectionMethod($config['class'], 'execute');
    } catch (ReflectionException $exception) {
      throw new CommandException($exception->getMessage());
    }
  }
}