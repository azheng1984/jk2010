<?php
class OptionObjectBuilder {
  private $config;
  private $argumentParser;

  public function __construct($config, $argumentParser) {
    $this->config = $config;
    $this->argumentParser = $argumentParser;
  }

  public function build() {
    $reflector = new ReflectionClass($this->config['class']);
    $constructor = $reflector->getConstructor();
    $standardLength = 0;
    if ($constructor !== null) {
      $standardLength = $constructor->getNumberOfParameters();
    }
    if (in_array('infinite', $this->config, true)) {
      $standardLength = null;
    }
    $arguments = $this->argumentParser->parse($standardLength);
    $length = count($arguments);
    if ($constructor === null && $length !== 0) {
      throw new CommandException("Option argument not allowed(input:$length)");
    }
    if ($constructor === null) {
      return $reflector->newInstance(); 
    }
    $verifier = new ArgumentVerifier;
    $verifier->verify($constructor, $length, $standardLength === null);
    return $reflector->newInstanceArgs($arguments);
  }
}