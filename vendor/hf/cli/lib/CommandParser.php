<?php
class CommandParser {
  private $config;
  private $shortOptions;
  private $arguments;
  private $argumentCount;
  private $currentIndex = 1;
  private $commandClass;
  private $commandArguments = array();

  public function run() {
    $this->argumentCount = $_SERVER['argc'];
    $this->arguments = $_SERVER['argv'];
    $this->config = require HF_CONFIG_PATH.__CLASS__.'.config.php';
    $this->buildShortOptions();
    if (!isset($this->config['command'])) {
      $this->commandClass = new $this->config['class'];
    }
    while ($this->currentIndex < $this->argumentCount) {
      $this->parseNext();
      ++$this->currentIndex;
    }
    if ($this->commandClass == null) {
      throw new Exception;
    }
    $function = array(new $this->commandClass, 'execute');
    call_user_func_array($function, $this->commandArguments);
  }

  private function parseNext() {
    $item = $this->arguments[$this->currentIndex];
    if ($this->startWith('-', $item)) {
      $this->buildOption($item);
      continue;
    }
    if ($this->commandClass == null) {
      $this->buildCommand($item);
      continue;
    }
    $this->commandArguments[] = $item;
  }

  private function buildOption($item) {
    $orignalKey = $item;
    $value = true;
    $pieces = explode('=', $item, 2);
    if (count($pieces) == 2) {
      $orignalKey = $pieces[0];
      $value = $pieces[1];
    }
    $name = $this->getOptionName($orignalKey);
    if (!isset($this->config['option'][$name])) {
      throw new Exception("Option '$orignalKey' not allowed");
    }
    if (isset($this->config['option'][$name]['expansion'])) {
      $this->expand($this->config[$name]['expansion']);
      return;
    }
    if (isset($this->config['option'][$name]['class'])) {
      $value = new $this->config['option'][$name]['class']($value);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getOptionName($orignalKey) {
    if (strlen($orignalKey) == 2) {
      return $this->shortOptions[substr($orignalKey, 1)];
    }
    return substr($orignalKey, 2);
  }

  private function buildCommand($item) {
    if (!isset($this->config['command'][$item])) {
      throw new Exception("Command '$item' not found");
    }
    $this->config = $this->config['command'][$item];
    if (!is_array($this->config)) {
      $this->commandClass = $this->config;
      $this->config = array('option' => array());
      return;
    }
    $this->buildShortOptions();
    $this->commandName = $this->config['class'];
  }

  private function buildShortOptions() {
    foreach ($this->config['option'] as $key => $value) {
      if (!isset($value['short'])) {
        continue;
      }
      $shortOptions = $value['short'];
      if (!is_array($shortOptions)) {
        $this->optionShortcuts[$shortOptions] = $key;
        continue;
      }
      foreach ($shortOptions as $item) {
        $this->optionShortcuts[$item] = $key;
      }
    }
  }

  private function expand($arguments) {
    array_splice($this->arguments, $this->currentIndex, 1, $arguments);
    $this->argumentCount = count($this->arguments);
    --$this->currentIndex;
  }

  private function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
  }
}