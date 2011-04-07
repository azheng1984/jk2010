<?php
class OptionParser {
  private $reader;
  private $config;
  private $nameParser;
  private $argumentParser;

  public function __construct($reader, $config) {
    $this->reader = $reader;
    $this->config = is_array($config) ? $config : array($config);
    $this->nameParser = new OptionNameParser($this->config);
    $this->argumentParser = new OptionArgumentParser($reader);
  }

  public function parse() {
    $item = $this->reader->get();
    $name = $this->nameParser->parse($item);
    if (is_array($name)) {
      $this->reader->expand($name);
      return;
    }
    $config = $this->getConfig($name);
    if ($config === null) {
      throw new CommandException("Option '$item' not allowed");
    }
    if (isset($config['expansion'])) {
      $this->reader->expand($config['expansion']);
      return;
    }
    $value = true;
    if (isset($config['class'])) {
      $value = $this->buildObject($item, $config);
    }
    return array($name, $value);
  }

  private function buildObject($item, $config) {
    $objectBuilder = new OptionObjectBuilder($config, $this->argumentParser);
    try {
      return $objectBuilder->build();
    } catch (CommandException $exception) {
      throw new CommandException("Option '$item':".$exception->getMessage());
    }
  }

  private function getConfig($name) {
    if (in_array($name, $this->config, true)) {
      return array();
    }
    if (!isset($this->config[$name])) {
      return;
    }
    if (!is_array($this->config[$name])) {
      return array('class' => $this->config[$name]);
    }
    return $this->config[$name];
  }
}