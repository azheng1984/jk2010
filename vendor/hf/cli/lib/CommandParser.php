<?php
class CommandParser {
  private $config;
  private $shortOptions;
  private $inputArguments;
  private $inputArgumentLength;
  private $currentIndex = 1;
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
    $isVariableLength = false;
    if (in_array('variable_length', $this->config)) {
      $isVariableLength = true;
    }
    $reflector = new ReflectionMethod($class, 'execute');
    $length = count($this->arguments);
    $this->verifyArguments($reflector, $length, $isVariableLength);
    $reflector->invokeArgs(new $class, $this->arguments);
  }

  private function parse($item) {
    if (strpos($item, '-') === 0) {
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
    $value = null;
    $pieces = explode('=', $item, 2);
    if (count($pieces) == 2) {
      $orignalKey = $pieces[0];
      $value = $pieces[1];
    }
    $name = $this->getOptionName($orignalKey);
    if (!isset($this->config['option'][$name])
     && !in_array($name, $this->config['option'], true)) {
      throw new Exception("Option '$orignalKey' not allowed");
    }
    if (isset($this->config['option'][$name]['expansion'])) {
      $this->expand($this->config['option'][$name]['expansion']);
      return;
    }
    if (isset($this->config['option'][$name]['class'])) {
      $class = $this->config['option'][$name]['class'];
      $value = $this->buildOptionObject($class, $value);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getOptionName($orignalKey) {
    if (strlen($orignalKey) == 2) {
      return $this->shortOptions[substr($orignalKey, 1)];
    }
    return substr($orignalKey, 2);
  }

  private function expand($arguments) {
    array_splice($this->inputArguments, $this->currentIndex, 1, $arguments);
    $this->inputArgumentLength = count($this->inputArguments);
    --$this->currentIndex;
  }

  private function buildOptionObject($class, $argument) {
    $reflector = new ReflectionClass($class);
    $constructor = $reflector->getConstructor();
    if ($constructor == null && $argument !== null) {
      throw new Exception;
    }
    if ($constructor != null) {
      $this->verifyArguments($constructor, $argument === null ? 0 : 1, false);
    }
    return $argument === null ? new $class : new $class($argument);
  }

  private function buildCommand($item) {
    if (!isset($this->config['command'][$item])) {
      throw new Exception("Command '$item' not found");
    }
    $this->readConfig($this->config['command'][$item]);
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

  private function verifyArguments($reflector, $length, $isVariableLength) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length == 0) {
        break;
      }
      --$length;
    }
    if ($length < 0) {
      throw new Exception;
    }
    if ($length > 0 && $isVariableLength == false) {
      throw new Exception;
    }
  }
}