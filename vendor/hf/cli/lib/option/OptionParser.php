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
    $class = null;
    if (in_array('string', $config)) {
      $class = 'StringOption';
    }
    if (isset($config['class'])) {
      $class = $config['class'];
    }
    $value = true;
    if ($class !== null) {
      $objectBuilder = new OptionObjectBuilder($this->argumentParser);
      $isInfiniteArgument = in_array('infinite_argument', $config, true);
      $value = $objectBuilder->build($class, $isInfiniteArgument);
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