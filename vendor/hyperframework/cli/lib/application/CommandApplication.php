<?php
class CommandApplication {
  private $config;
  private $reader;
  private $isAllowOption = true;
  private $optionParser;
  private $options = array();
  private $arguments = array();

  public function __construct($configPath = CONFIG_PATH) {
    $this->config = require $configPath.'command_application.config.php';
    $this->reader = new CommandReader;
    $this->initialize($this->config);
  }

  public function run() {
    while (($item = $this->reader->get()) !== null) {
      $this->parse($item);
      $this->reader->moveToNext();
    }
    $runner = new CommandRunner;
    return $runner->run($this->config, $this->options, $this->arguments);
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
    if (($result = $this->optionParser->parse()) !== null) {
      list($name, $value) = $result;
      $this->options[$name] = $value;
    }
  }

  private function setCommand($name) {
    if (!isset($this->config['children'][$name])) {
      throw new CommandException("Command '$name' not found");
    }
    $this->initialize($this->config['children'][$name]);
    $this->optionParser = null;
    $this->isAllowOption = true;
  }
}
