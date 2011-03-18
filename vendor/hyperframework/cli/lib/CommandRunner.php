<?php
class CommandRunner {
  public function run($config, $arguments) {
    if (isset($config['sub'])) {
      $_ENV['command_writer'] = new CommandWriter;
      $profile = new PackageProfile;
      return $profile->render($config);
    }
    if (!isset($config['class'])) {
      throw new CommandException('command class not found');
    }
    $reflector = new ReflectionMethod($config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($arguments);
    $isInfiniteArgument = in_array('infinite_argument', $config, true);
    $verifier->verify($reflector, $length, $isInfiniteArgument);
    return $reflector->invokeArgs(new $config['class'], $arguments);
  }
}