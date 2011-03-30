<?php
class CommandApplication {
  private $config;
  private $reader;
  private $arguments = array();
  private $isAllowOption = true;
  private $optionParser;

  public function __construct() {
    $this->reader = new CommandReader;
    $this->config = require CONFIG_PATH.'command_application.config.php';
    $this->initialize($this->config);
  }

  public function run() {
    while (($item = $this->reader->get()) !== null) {
      $this->parse($item);
      $this->reader->moveToNext();
    }
    $runner = new CommandRunner;
    return $runner->run($this->config, $this->arguments);
  }

  private function initialize($config) {
    if (!is_array($config)) {
      $this->config = array('class' => $config);
      return;
    }
    if (isset($config['expansion'])) {
      $this->reader->expand($config['expansion']);
      return;
    }
    $this->config = $config;
  }

  private function parse($item) {
    if ($item === '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($this->isAllowOption && $item !== '-' && strpos($item, '-') === 0) {
      $this->parseOption();
      return;
    }
    if (!isset($this->config['class'])) {
      $this->setCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function parseOption() {
    if ($this->optionParser === null) {
      $this->optionParser = new OptionParser(
        $this->reader,
        isset($this->config['option']) ? $this->config['option'] : null
      );
    }
    $this->optionParser->parse();
  }

  private function setCommand($name) {
    if (!isset($this->config['sub'][$name])) {
      throw new CommandException("Command '$name' not found");
    }
    $this->initialize($this->config['sub'][$name]);
    $this->isAllowOption = true;
    $this->optionParser = null;
  }
}