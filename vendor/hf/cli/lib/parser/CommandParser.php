<?php
class CommandParser {
  private $reader;
  private $optionParser;
  private $config;
  private $isAfterCommand;
  private $isAllowOption = true;
  private $arguments = array();

  public function __construct($context) {
    $_ENV['context'] = $context;
    $this->reader = new CommandReader;
    $this->setConfig(require HF_CONFIG_PATH.'cli'
                            .DIRECTORY_SEPARATOR.__CLASS__.'.config.php');
  }

  public function run() {
    while (($item = $this->reader->read()) !== null) {
      $this->parse($item);
      $this->reader->move();
    }
    if (!isset($this->config['class'])
     && isset($this->config['default_command'])) {
      $this->setCommand($this->config['default_command']);
    }
    $runner = new CommandRunner;
    $runner->run($this->config, $this->arguments);
  }

  private function parse($item) {
    if ($item === '--') {
      $this->isAllowOption = false;
      return;
    }
    if ($item !== '-' && strpos($item, '-') === 0 && $this->isAllowOption) {
      $this->optionParser->run();
      return;
    }
    if (!$this->isAfterCommand) {
      $this->setCommand($item);
      return;
    }
    $this->arguments[] = $item;
  }

  private function setCommand($item) {
    if (!isset($this->config['command'][$item])) {
      throw new SyntaxException("Command '$item' not found");
    }
    $this->setConfig($this->config['command'][$item]);
    $this->isAllowOption = true;
  }

  private function setConfig($value) {
    if (!is_array($value)) {
      $value = array('class' => $value, 'option' => array());
    }
    $this->isAfterCommand = isset($value['class']);
    $this->optionParser = new OptionParser($this->reader,
                                           $value['option'],
                                           $this->isAfterCommand);
    $this->config = $value;
  }
}