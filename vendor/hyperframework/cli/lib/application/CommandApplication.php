<?php
class CommandApplication {
  private $config;
  private $reader;
  private $optionParser;
  private $isAllowOption = true;
  private $arguments = array();

  public function __construct() {
    $this->reader = new CommandReader;
    $this->setConfig(require CONFIG_PATH.'command_application.config.php');
  }

  public function run() {
    while (($item = $this->reader->get()) !== null) {
      $this->parse($item);
      $this->reader->moveToNext();
    }
    $runner = new CommandRunner;
    return $runner->run($this->config, $this->arguments);
  }

  private function parse($item) {
    if ($item === '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($this->isAllowOption && $item !== '-' && strpos($item, '-') === 0) {
      $this->optionParser->parse();
      return;
    }
    if (!isset($this->config['class'])) {
      $this->setCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function setCommand($item) {
    if (!isset($this->config['sub'][$item])) {
      throw new CommandException("Command '$item' not found");
    }
    $this->setConfig($this->config['sub'][$item]);
    $this->isAllowOption = true;
  }

  private function setConfig($value) {
    if (!is_array($value)) {
      $value = array('class' => $value, 'option' => array());
    }
    $optionConfig = isset($value['option']) ? $value['option'] : array();
    $this->optionParser = new OptionParser($this->reader, $optionConfig);
    if (isset($value['expansion'])) {
      $this->reader->expand($value['expansion']);
    }
    $this->config = $value;
  }
}