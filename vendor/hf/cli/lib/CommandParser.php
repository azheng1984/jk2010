<?php
class CommandParser {
  private $config;
  private $optionParser;
  private $reader;
  private $isCommandFound = false;
  private $isAllowOption = true;
  private $arguments = array();

  public function __construct() {
    $this->reader = new CommandReader;
    $this->readConfig(require HF_CONFIG_PATH.__CLASS__.'.config.php');
  }

  public function run() {
    while (($item = $this->reader->getItem())!== null) {
      $this->parse($item);
      $this->move();
    }
    $this->executeCommand();
  }

  private function parse($item) {
    if ($item == '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($item != '-' && strpos($item, '-') === 0 && $this->isAllowOption) {
      $this->optionParser->run();
      return;
    }
    if (!$this->isCommandFound) {
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
    $this->isCommandFound = isset($value['class']);
    $this->optionParser = new OptionParser($this->reader,
                                           $value['option'],
                                           $this->isCommandFound);
    $this->config = $value;
  }
}