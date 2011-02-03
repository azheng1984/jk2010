<?php
class CommandParser {
  private $config;
  private $optionMapping;
  private $values;
  private $count;
  private $currentIndex = 1;
  private $command;
  private $arguments = array();

  public function run() {
    $this->count = $_SERVER['argc'];
    $this->values = $_SERVER['argv'];
    $this->config = require HF_CONFIG_PATH.__CLASS__.'.config.php';
    $this->buildOptionMapping();
    if (!isset($this->config['command'])) {
      $this->command = $this->config['class'];
    }
    while ($this->currentIndex < $this->count) {
      $this->parse();
      ++$this->currentIndex;
    }
    if ($this->command == null) {
      throw new Exception;
    }
    $function = array(new $this->command, 'execute');
    call_user_func_array($function, $this->arguments);
  }

  private function parse() {
    $item = $this->values[$this->index];
    if ($this->startWith('-', $item)) {
      $this->buildOption($item);
      continue;
    }
    if ($this->command == null) {
      $this->buildCommand($item);
      continue;
    }
    $this->arguments[] = $item;
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
    if (isset($this->config[$name]['expansion'])) {
      $this->expand($this->config[$name]['expansion']);
      return;
    }
    if (isset($this->config[$name]['class'])) {
      $value = new $this->config[$name]['class']($value);
    }
    $_ENV['context']->addOption($name, $value);
  }

  private function getOptionName($orignalKey) {
    if (strlen($orignalKey) == 2) {
      return $this->optionMapping[substr($orignalKey, 1)];
    }
    return substr($orignalKey, 2);
  }

  private function buildCommand($item) {
    $this->config = $this->config['command'][$item];
    $this->buildOptionMapping();
    $this->command = $item;
  }

  private function buildOptionMapping() {
    foreach ($this->config['option'] as $key => $value) {
      if (!isset($value['shortcut'])) {
        continue;
      }
      $shortcuts = $value['shortcut'];
      if (!is_array($shortcuts)) {
        $shortcuts = array($shortcuts);
      }
      foreach ($shortcuts as $item) {
        $this->optionMapping[$item] = $key;
      }
    }
  }

  private function expand($values) {
    array_splice($this->values, $this->currentIndex, 1, $values);
    $this->count = count($this->values);
    --$this->currentIndex;
  }

  private function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
  }
}