<?php
class CommandParser {
  private $config;
  private $reader;
  private $optionParser;
  private $isAfterLeaf;
  private $isAllowOption = true;
  private $arguments = array();

  public function __construct($context) {
    $_ENV['context'] = $context;
    $this->reader = new CommandReader;
    $this->setConfig(require HF_CONFIG_PATH.'cli'
                            .DIRECTORY_SEPARATOR.__CLASS__.'.config.php');
  }

  public function parse() {
    while (($item = $this->reader->read()) !== null) {
      $this->analyze($item);
      $this->reader->move();
    }
    if (!isset($this->config['class']) && isset($this->config['default_sub'])) {
      $this->reader->expand($this->config['default_sub'])->move();
      return $this->parse();
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
    if (!$this->isAfterLeaf) {
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
      $value = array('class' => $value, 'option' => array ());
    }
    $this->isAfterLeaf = isset($value['class']);
    $this->optionParser = new OptionParser($this->reader,
                                           $value['option'],
                                           $this->isAfterLeaf);
    if (isset($value['expansion'])) {
      $this->reader->expand($value['expansion']);
    }
    $this->config = $value;
  }
}