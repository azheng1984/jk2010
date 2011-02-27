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
    $config = null;
    if (in_array($name, $this->config, true)) {
      $config = array();
    }
    if (isset($this->config[$name])) {
      $config = $this->config[$name];
    }
    if ($config === null) {
      throw new SyntaxException("Option '$item' not allowed");
    }
    if (isset($config['expansion'])) {
      $this->reader->expand($config['expansion']);
      return;
    }
    if (in_array('string', $config)) {
      $config['class'] = 'StringOption';
    }
    $value = true;
    if (isset($config['class'])) {
      $objectBuilder = new OptionObjectBuilder($this->argumentParser);
      $value = $objectBuilder->build($config);
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