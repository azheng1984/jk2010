<?php
class OptionParser {
  private $reader;
  private $config;
  private $shorts = array();
  private $isAfterCommand;

  public function __construct($reader, $config, $isAfterCommand) {
    $this->reader = $reader;
    $this->config = $config;
    $this->isAfterCommand = $isAfterCommand;
  }

  public function run() {
    $item = $this->reader->get();
    $name = $this->getName($item);
    if (is_array($name)) {
      $this->reader->expand($name);
      return;
    }
    if (!isset($this->config[$name]) && !in_array($name, $this->config, true)) {
      throw new Exception("Option '$item' not allowed");
    }
    if (isset($this->config[$name]['expansion'])) {
      $this->reader->expand($this->config[$name]['expansion']);
      return;
    }
    $value = true;
    if (isset($this->config[$name]['class'])) {
      $value = $this->buildOption($this->config[$name]);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function buildOption($config) {
    $reflector = new ReflectionClass($config['class']);
    $constructor = $reflector->getConstructor();
    $maximumLength = 0;
    if ($constructor !== null) {
      $maximumLength = $constructor->getNumberOfParameters();
    }
    if (in_array('infinite_argument', $config, true)) {
      $maximumLength = null;
    }
    $arguments = $this->getArguments($maximumLength);
    if ($constructor === null && count($arguments) !== 0) {
      throw new Exception("Option argument length not matched");
    }
    if ($constructor !== null) {
      $length = count($arguments);
      $verifier = new ArgumentVerifier;
      if ($verifier->run($constructor, $length, $maximumLength === null)) {
        return $reflector->newInstanceArgs($arguments);
      }
    }
    return null;
  }
}