<?php
class CommandParser {
  private $config;
  private $shortOptions;
  private $inputArguments;
  private $inputArgumentLength;
  private $currentIndex = 1;
  private $isArgumentOnly = false;
  private $arguments = array();

  public function run() {
    $this->inputArgumentLength = $_SERVER['argc'];
    $this->inputArguments = $_SERVER['argv'];
    $this->readConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
    while ($this->currentIndex < $this->inputArgumentLength) {
      $this->parse($this->inputArguments[$this->currentIndex]);
      ++$this->currentIndex;
    }
    $class = $this->config['class'];
    $isInfiniteLength = false;
    if (in_array('infinite_length', $this->config)) {
      $isInfiniteLength = true;
    }
    $reflector = new ReflectionMethod($class, 'execute');
    $length = count($this->arguments);
    $this->verifyArguments($reflector, $length, $isInfiniteLength);
    $reflector->invokeArgs(new $class, $this->arguments);
  }

  private function parse($item) {
    if (strpos($item, '-') === 0 && !$this->isArgumentOnly) {
      $this->buildOption($item);
      return;
    }
    if (!isset($this->config['class'])) {
      $this->buildCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function buildOption($item) {
    $orignalKey = $item;
    if ($item == '--') {
      $this->isArgumentOnly = true;
      return;
    }
    $name = $this->getOptionName($orignalKey);
    if (is_array($name)) {
      $this->expand($name);
      return;
    }
    if (!isset($this->config['option'][$name])
     && !in_array($name, $this->config['option'], true)) {//?
      throw new Exception("Option '$orignalKey' not allowed");
    }
    if (isset($this->config['option'][$name]['expansion'])) {
      $this->expand($this->config['option'][$name]['expansion']);
      return;
    }
    $value = null;
    if (isset($this->config['option'][$name]['class'])) {
      $class = $this->config['option'][$name]['class'];
      $isVariableLength = false;
      if (in_array('infinite_length', $this->config['option'][$name])) {
        $isVariableLength = true;
      }
      $value = $this->buildOptionObject($class, $isVariableLength);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getOptionName($orignalKey) {
    if (strpos($orignalKey, '--') === 0) {
      return substr($orignalKey, 2);
    }
    $shortOptions = substr($orignalKey, 1);
    if (strlen($shortOptions) == 1) {
      return $this->getOptionFullName($shortOptions);
    }
    $options = array();
    foreach (str_split($shortOptions) as $item) {
      $options[] = '-'.$item;
    }
    return $options;
  }

  private function getOptionFullName($shortName) {
    if (!isset($this->shortOptions[$shortName])) {
      throw new Exception("Option '$shortName' not allowed");
    }
    return $this->shortOptions[$shortName];
  }

  private function expand($arguments) {
    array_splice($this->inputArguments, $this->currentIndex, 1, $arguments);
    $this->inputArgumentLength = count($this->inputArguments);
    --$this->currentIndex;
  }

  private function buildOptionObject($class, $isInfiniteLength) {
    $reflector = new ReflectionClass($class);
    $constructor = $reflector->getConstructor();
    $maximumArgumentLength = 0;
    if ($constructor != null) {
      $maximumArgumentLength = $constructor->getNumberOfParameters();
    }
    if ($isInfiniteLength) {
      $maximumArgumentLength = null;
    }
    $arguments = $this->readOptionArguments($maximumArgumentLength);
    if ($constructor == null && count($arguments) !== 0) {
      throw new Exception;
    }
    if ($constructor != null) {
      $this->verifyArguments($constructor, count($arguments), false);
    }
    return $reflector->newInstanceArgs($arguments);
  }

  private function readOptionArguments($maximumLength) {
    $arguments = array();
    while ($this->currentIndex < $this->inputArgumentLength) {
      $item = $this->inputArgumentLength[++$this->currentIndex];
      if (strpos($item, '-') === 0) {
        break;
      }
      $arguments[] = $item;
    }
    $count = count($arguments);
    if ($maximumLength !== null
     && $count > $maximumLength
     && $this->currentIndex == $this->inputArgumentLength) {
      $this->arguments = array_slice($arguments, $maximumLength);
      return array_slice($arguments, 0, $maximumLength);
    }
    if ($maximumLength !== null
     && $count == $maximumLength + 1
     && !isset($this->config['class'])) {
      --$this->currentIndex;
      array_pop($arguments);
    }
    return $arguments;
  }

  private function buildCommand($item) {
    if (!isset($this->config['command'][$item])) {
      throw new Exception("Command '$item' not found");
    }
    $this->readConfig($this->config['command'][$item]);
    $this->isArgumentOnly = false;
  }

  private function readConfig($value) {
    if (!is_array($value)) {
      $value = (array('class' => $value, 'option' => array()));
    }
    $this->config = $value;
    foreach ($this->config['option'] as $key => $value) {
      if (!isset($value['short'])) {
        continue;
      }
      $shortOptions = $value['short'];
      if (!is_array($shortOptions)) {
        $this->shortOptions[$shortOptions] = $key;
        continue;
      }
      foreach ($shortOptions as $item) {
        $this->shortOptions[$item] = $key;
      }
    }
  }

  private function verifyArguments($reflector, $length, $isInfiniteLength) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length == 0) {
        break;
      }
      --$length;
    }
    if ($length < 0) {
      throw new Exception;
    }
    if ($length > 0 && $isInfiniteLength == false) {
      throw new Exception;
    }
  }
}