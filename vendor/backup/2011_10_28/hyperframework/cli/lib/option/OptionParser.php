<?php
class OptionParser {
  private $reader;
  private $config;
  private $nameParser;

  public function __construct($reader, $config) {
    $this->reader = $reader;
    $this->config = is_array($config) ? $config : array($config);
    $this->nameParser = new OptionNameParser($this->config);
  }

  public function parse() {
    $item = $this->reader->get();
    $name = $this->nameParser->parse($item);
    if (is_array($name)) {
      $this->reader->expand($name);
      return;
    }
    $config = $this->getItemConfig($name);
    if ($config === null) {
      throw new CommandException("Option '$item' not allowed");
    }
    if (isset($config['expansion'])) {
      $this->reader->expand($config['expansion']);
      return;
    }
    if (isset($config['class'])) {
      return array($name, $this->buildObject($item, $config));
    }
    return array($name, true);
  }

  private function buildObject($item, $config) {
    $objectBuilder = new OptionObjectBuilder($config, $this->reader);
    try {
      return $objectBuilder->build();
    } catch (CommandException $exception) {
      throw new CommandException("Option '$item':".$exception->getMessage());
    }
  }

  private function getItemConfig($name) {
    if ($name === null) {
      return;
    }
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