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
    $config = $this->getConfig($name);
    if ($config === null) {
      throw new SyntaxException("Option '$item' not allowed");
    }
    if (isset($config['expansion'])) {
      $this->reader->expand($config['expansion']);
      return;
    }
    $value = true;
    if (isset($config['class'])) {
      $objectBuilder = new OptionObjectBuilder($this->argumentParser);
      $value = $objectBuilder->build($config);
    }
    $this->setOption($name, $value);
  }

  private function getConfig($name) {
    if (in_array($name, $this->config, true)) {
      return array();
    }
    if (isset($this->config[$name])) {
      return $this->config[$name];
    }
  }

  private function setOption($name, $value) {
    if (!isset($_ENV['option'])) {
      $_ENV['option'] = array();
    }
    $_ENV['option'][$name] = $value;
  }
}