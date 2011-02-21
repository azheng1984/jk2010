<?php
class OptionObjectBuilder {
  private $argumentParser;

  public function __construct($argumentParser) {
    $this->argumentParser = $argumentParser;
  }

  public function build($config) {
    $reflector = new ReflectionClass($config['class']);
    $constructor = $reflector->getConstructor();
    $maximumLength = 0;
    if ($constructor !== null) {
      $maximumLength = $constructor->getNumberOfParameters();
    }
    if (in_array('infinite_argument', $config, true)) {
      $maximumLength = null;
    }
    $arguments = $this->argumentParser->parse($maximumLength);
    if ($constructor === null && count($arguments) !== 0) {
      throw new SyntaxException("Option argument length not matched");
    }
    if ($constructor === null) {
      return $reflector->newInstance(); 
    }
    $length = count($arguments);
    $verifier = new ArgumentVerifier;
    if ($verifier->verify($constructor, $length, $maximumLength === null)) {
      return $reflector->newInstanceArgs($arguments);
    }
    return null;
  }
}