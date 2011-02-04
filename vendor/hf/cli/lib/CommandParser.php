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
    $this->setConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
    if (isset($this->config['class'])) {
      $this->commandClass = new $this->config['class'];
    }
    while ($this->currentIndex < $this->argumentCount) {
      $this->parse();
      ++$this->currentIndex;
    }
    if ($this->commandClass == null) {
      throw new Exception('Command not found');
    }
    $function = array(new $this->commandClass, 'execute');
    call_user_func_array($function, $this->commandArguments);
  }

  private function parse() {
    $item = $this->arguments[$this->currentIndex];
    if ($this->startsWith($item, '-')) {
      $this->buildOption($item);
      return;
    }
    if ($this->commandClass == null) {
      $this->buildCommand($item);
      return;
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
    if (!isset($this->config['option'][$name])
     && !in_array($name, $this->config['option'], true)) {
      throw new Exception("Option '$orignalKey' not allowed");
    }
    if (isset($this->config['option'][$name]['expansion'])) {
      $this->expand($this->config['option'][$name]['expansion']);
      return;
    }
    if ($value === true && isset($this->config['option']['default'])) {
      $value = $this->config['option']['default'];
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
    $config = $this->config['command'][$item];
    if (!is_array($config)) {
      $this->commandClass = $config;
      $this->setConfig(array('option' => array()));
      return;
    }
    $this->commandClass = $this->config['class'];
    $this->setConfig($config);
  }

  private function setConfig($value) {
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

  private function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
  }
}