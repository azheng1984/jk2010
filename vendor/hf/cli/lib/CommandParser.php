<?php
class CommandParser {
  private $config;
  private $shortOptions;
  private $arguments;
  private $argumentCount;
  private $currentIndex = 1;
  private $commandArguments = array();

  public function run() {
    $this->argumentCount = $_SERVER['argc'];
    $this->arguments = $_SERVER['argv'];
    $this->readConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
    while ($this->currentIndex < $this->argumentCount) {
      $this->parse();
      ++$this->currentIndex;
    }
    if ($this->config['class'] == null) {
      throw new Exception('Command not found');
    }
    $class = $this->config['class'];
    $isVariableLength = false;
    if (in_array('variable_length', $this->config)) {
      $isVariableLength = true;
    }
    $reflector = new ReflectionMethod($class, 'execute');
    $length = count($this->commandArguments);
    $this->verifyArguments($reflector, $length, $isVariableLength);
    $reflector->invokeArgs(new $class, $this->commandArguments);
  }

  private function parse() {
    $item = $this->arguments[$this->currentIndex];
    if ($this->startsWith($item, '-')) {
      $this->buildOption($item);
      return;
    }
    if (!isset($this->config['class'])) {
      $this->buildCommand($item);
      return;
    }
    $this->commandArguments[] = $item;
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
    $config = $this->config['command'][$item];
    if (!is_array($config)) {
      $config = (array('class' => $config, 'option' => array()));
    }
    $this->readConfig($config);
  }

  private function readConfig($value) {
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

  private function expand($arguments) {
    array_splice($this->arguments, $this->currentIndex, 1, $arguments);
    $this->argumentCount = count($this->arguments);
    --$this->currentIndex;
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

  private function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
  }
}