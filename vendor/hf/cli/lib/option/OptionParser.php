<?php
class OptionParser {
  private $reader;
  private $config;
  private $nameParser;
  private $argumentParser;

  public function __construct($reader, $config, $isLastCommand) {
    $this->reader = $reader;
    $this->config = $config;
    $this->nameParser = new OptionNameParser($config);
    $this->argumentParser = new OptionArgumentParser($reader, $isLastCommand);
  }

  public function parse() {
    $item = $this->reader->read();
    $name = $this->nameParser->parse($item);
    if (is_array($name)) {
      $this->reader->expand($name);
      return;
    }
    if (!isset($this->config[$name]) && !in_array($name, $this->config, true)) {
      throw new SyntaxException("Option '$item' not allowed");
    }
    if (isset($this->config[$name]['expansion'])) {
      $this->reader->expand($this->config[$name]['expansion']);
      return;
    }
    $value = true;
    if (isset($this->config[$name]['class'])) {
      $objectBuilder = new OptionObjectBuilder($this->argumentParser);
      $value = $objectBuilder->build($this->config[$name]);
    }
    $this->setOption($name, $value);
  }

  private function setOption($name, $value) {
    if (!isset($_ENV['option'])) {
      $_ENV['option'] = array();
    }
    $_ENV['option'][$name] = $value;
  }
}