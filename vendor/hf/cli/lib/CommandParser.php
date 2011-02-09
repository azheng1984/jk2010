<?php
class CommandParser {
  private $config;
  private $optionParser;
  private $inputArguments;
  private $inputArgumentLength;
  private $currentIndex = 1;
  private $isAllowOption = true;
  private $arguments = array();

  public function run() {
    $this->readConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
    while ($this->currentIndex < $this->inputArgumentLength) {
      $this->parse($this->inputArguments[$this->currentIndex]);
      ++$this->currentIndex;
    }
    $this->executeCommand();
  }

  private function parse($item) {
    if ($item == '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($item != '-' && strpos($item, '-') === 0 && $this->isAllowOption) {
      $this->optionParser->run($item);
      return;
    }
    if (!isset($this->config['class'])) {
      $this->buildCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function buildCommand($item) {
    if (!isset($this->config['command'][$item])) {
      throw new Exception("Command '$item' not found");
    }
    $this->readConfig($this->config['command'][$item]);
    $this->isAllowOption = true;
  }

  private function executeCommand() {
    if (!isset($this->config['class'])) {
      throw new Exception;
    }
    $isInfiniteLength = false;
    if (in_array('infinite_length', $this->config)) {
      $isInfiniteLength = true;
    }
    $reflector = new ReflectionMethod($this->config['class'], 'execute');
    $length = count($this->arguments);
    $this->verifyArguments($reflector, $length, $isInfiniteLength);
    $reflector->invokeArgs(new $this->config['class'], $this->arguments);
  }

  private function readConfig($value) {
    if (!is_array($value)) {
      $value = (array('class' => $value, 'option' => array()));
    }
    $this->optionParser = new OptionParser($value['option']);
    $this->config = $value;
  }
}