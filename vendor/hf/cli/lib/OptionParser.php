<?php
class OptionParser {
  private $reader;
  private $config;
  private $isCommandContext;
  private $shorts = array();

  public function __construct($reader, $config, $isCommandContext) {
    $this->reader = $reader;
    $this->config = $config;
    $this->isCommandContext = $isCommandContext;
    foreach ($config as $key => $value) {
      if (!isset($value['short'])) {
        continue;
      }
      $shorts = $value['short'];
      if (!is_array($shorts)) {
        $this->shorts[$shorts] = $key;
        continue;
      }
      foreach ($shorts as $item) {
        $this->shorts[$item] = $key;
      }
    }
  }

  public function run() {
    $item = $this->reader->getItem();
    $name = $this->getName($item);
    if (is_array($name)) {
      $this->reader->expand($name);
      return;
    }
    if (!isset($this->config[$name])
     && !in_array($name, $this->config, true)) {
      throw new Exception("Option '$item' not allowed");
    }
    if (isset($this->config[$name]['expansion'])) {
      $this->reader->expand($this->config[$name]['expansion']);
      return;
    }
    $value = null;
    if (isset($this->config[$name]['class'])) {
      $isInfinite = false;
      if (in_array('infinite_argument', $this->config[$name])) {
        $isInfinite = true;
      }
      $class = $this->config[$name]['class'];
      $value = $this->buildOptionInstance($class, $isInfinite);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getName($item) {
    if (strpos($item, '--') === 0) {
      return substr($item, 2);
    }
    $shorts = substr($item, 1);
    if (strlen($shorts) == 1) {
      return $this->getFullName($shorts);
    }
    $options = array();
    foreach (str_split($shorts) as $item) {
      $options[] = '-'.$item;
    }
    return $options;
  }

  private function getFullName($shortName) {
    if (!isset($this->shorts[$shortName])) {
      throw new Exception("Option '$shortName' not allowed");
    }
    return $this->shorts[$shortName];
  }

  private function buildOptionInstance($class, $isInfinite) {
    $reflector = new ReflectionClass($class);
    $constructor = $reflector->getConstructor();
    $maximumLength = 0;
    if ($constructor != null) {
      $maximumLength = $constructor->getNumberOfParameters();
    }
    if ($isInfinite) {
      $maximumLength = null;
    }
    $arguments = $this->getArguments($maximumLength);
    if ($constructor == null && count($arguments) !== 0) {
      throw new Exception("Option argument length not matched");
    }
    if ($constructor != null) {
      $length = count($arguments);
      $verifier = new ArgumentVerifier;
      $verifier->run($constructor, $length, $isInfinite);
    }
    return $reflector->newInstanceArgs($arguments);
  }

  private function getArguments($maximumLength) {
    $arguments = array();
    while (($item = $this->reader->getItem()) !== null) {
      if (strpos($item, '-') === 0 && $item != '-') {
        $this->reader->move(-1);
        break;
      }
      $arguments[] = $item;
      $this->reader->move();
    }
    $amount = count($arguments);
    if ($amount > $maximumLength && $maximumLength !== null) {
      return $this->cutArguments($arguments, $amount, $maximumLength);
    }
    return $arguments;
  }

  private function cutArguments($arguments, $amount, $maximumLength) {
    if ($amount == $maximumLength + 1 && !$this->isCommandContext) {
      array_pop($arguments);
      $this->reader->move(-1);
      return $arguments;
    }
    if ($this->reader->getItem() === null) {
      $arguments = array_slice($arguments, 0, $maximumLength);
      $this->reader->move($maximumLength - $amount);
    }
    return $arguments;
  }
}