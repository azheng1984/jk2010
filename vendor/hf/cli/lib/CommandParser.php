<?php
class CommandParser {
  private $config;
  private $optionParser;
  private $reader;
  private $isAllowOption = true;
  private $arguments = array();

  public function run() {
    $this->readConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
    $this->reader = new CommandReader;
    while (($item = $this->reader->getItem())!== null) {
      $this->parse($item);
      $this->next();
    }
    $this->executeCommand();
  }

  private function parse($item) {
    if ($item == '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($item != '-' && strpos($item, '-') === 0 && $this->isAllowOption) {
      return $this->optionParser->run($this->reader);
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
    $reflector = new ReflectionMethod($this->config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($this->arguments);
    $isInfinite = in_array('infinite_argument', $this->config);
    $verifier->run($reflector, $length, $isInfinite);
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