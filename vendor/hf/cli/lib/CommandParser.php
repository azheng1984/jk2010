<?php
class CommandParser {
  private $config;
  private $reader;
  private $optionParser;
  private $isLast;
  private $isAllowOption = true;
  private $arguments = array();

  public function __construct() {
    $this->setConfig(require CONFIG_PATH.'command_parser.config.php');
    $this->reader = new CommandReader;
  }

  public function parse() {
    while (($item = $this->reader->read()) !== null) {
      $this->analyze($item);
      $this->reader->move();
    }
    $runner = new CommandRunner;
    return $runner->run($this->config, $this->arguments);
  }

  private function analyze($item) {
    if ($item === '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($item !== '-' && strpos($item, '-') === 0 && $this->isAllowOption) {
      $this->optionParser->parse();
      return;
    }
    if (!$this->isLast) {
      $this->setCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function setCommand($item) {
    if (!isset($this->config['sub'][$item])) {
      throw new SyntaxException("Command '$item' not found");
    }
    $this->setConfig($this->config['sub'][$item]);
    $this->isAllowOption = true;
  }

  private function setConfig($value) {
    if (!is_array($value)) {
      $value = array('class' => $value, 'option' => array());
    }
    $this->isLast = isset($value['class']);
    $optionConfig = isset($value['option']) ? $value['option'] : array();
    $this->optionParser = new OptionParser(
      $this->reader, $optionConfig, $this->isLast
    );
    if (isset($value['expansion'])) {
      $this->reader->expand($value['expansion']);
    }
    $this->config = $value;
  }
}