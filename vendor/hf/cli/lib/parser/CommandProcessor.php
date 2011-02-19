<?php
class CommandProcessor {
  private function executeCommand() {
    if (!isset($this->config['class'])) {
      throw new SyntaxException;
    }
    $reflector = new ReflectionMethod($this->config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($this->arguments);
    $isInfinite = in_array('infinite_argument', $this->config, true);
    $verifier->run($reflector, $length, $isInfinite);
    $reflector->invokeArgs(new $this->config['class'], $this->arguments);
  }
}