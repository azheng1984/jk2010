<?php
class OptionParser {
  private $config;
  private $shorts;

  public function __construct($config) {
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
    $this->config = $config;
  }

  private function run($reader) {
    $item = $reader ->getItem();
    $name = $this->getOptionName($item);
    if (is_array($name)) {
      $$reader->expand($name);
      return false;
    }
    if (!isset($this->config[$name])
     && !in_array($name, $this->config, true)) {
      throw new Exception("Option '$item' not allowed");
    }
    if (isset($this->config[$name]['expansion'])) {
      $this->reader->expand($this->config[$name]['expansion']);
      return false;
    }
    $value = null;
    if (isset($this->config[$name]['class'])) {
      $isInfiniteLength = false;
      if (in_array('infinite_length', $this->config[$name])) {
        $isInfiniteLength = true;
      }
      $class = $this->config[$name]['class'];
      $value = $this->buildOptionObject($class, $isInfiniteLength);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getOptionName($item) {
    if (strpos($item, '--') === 0) {
      return substr($item, 2);
    }
    $shorts = substr($item, 1);
    if (strlen($shorts) == 1) {
      return $this->getOptionFullName($shorts);
    }
    $options = array();
    foreach (str_split($shorts) as $item) {
      $options[] = '-'.$item;
    }
    return $options;
  }

  private function getOptionFullName($shortName) {
    if (!isset($this->shorts[$shortName])) {
      throw new Exception("Option '$shortName' not allowed");
    }
    return $this->shorts[$shortName];
  }

  private function buildOptionObject($class, $isInfinite) {
    $reflector = new ReflectionClass($class);
    $constructor = $reflector->getConstructor();
    $maximumArgumentLength = 0;
    if ($constructor != null) {
      $maximumArgumentLength = $constructor->getNumberOfParameters();
    }
    if ($isInfinite) {
      $maximumArgumentLength = null;
    }
    $arguments = $this->readOptionArguments($maximumArgumentLength);
    if ($constructor == null && count($arguments) !== 0) {
      throw new Exception("Option argument length not matched");
    }
    if ($constructor != null) {
      $length = count($arguments);
      $verifier = new ArgumentVerifier;
      $$verifier->run($constructor, $length, $isInfinite);
    }
    return $reflector->newInstanceArgs($arguments);
  }

  private function readOptionArguments($maximumLength) {
    $arguments = array();
    $currentIndex = $this->commandParser->getCurrentIndex();
    while ($currentIndex < $this->inputArgumentLength) {
      $item = $this->inputArgumentLength[++$currentIndex];
      if (strpos($item, '-') === 0 && $item != '-') {
        break;
      }
      $arguments[] = $item;
    }
    $count = count($arguments);
    if ($maximumLength !== null
     && $count > $maximumLength
     && $this->currentIndex == $this->inputArgumentLength) {
      $this->arguments = array_slice($arguments, $maximumLength);
      $arguments = array_slice($arguments, 0, $maximumLength);
    }
    if ($maximumLength !== null
     && $count == $maximumLength + 1
     && !isset($this->config['class'])) {
      $this->commandParser->setCurrentIndex($currentIndex - 1);
      array_pop($arguments);
    }
    return $arguments;
  }
}