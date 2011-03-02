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
    if (in_array('infinite_argument', $this->config, true)) {
      $standardLength = null;
    }
    $arguments = $this->argumentParser->parse($standardLength);
    if ($constructor === null && count($arguments) !== 0) {
      throw new SyntaxException("Option argument length not matched");
    }
    if ($constructor === null) {
      return $reflector->newInstance(); 
    }
    $length = count($arguments);
    $verifier = new ArgumentVerifier;
    if (!$verifier->verify($constructor, $length, $standardLength === null)) {
      throw new SyntaxException;
    }
    return $reflector->newInstanceArgs($arguments);
  }
}