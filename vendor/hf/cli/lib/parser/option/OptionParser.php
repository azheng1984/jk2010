<?php
class OptionParser {
  private $reader;
  private $config;
  private $nameParser;
  private $builder;

  public function __construct($reader, $config, $isAfterCommand) {
    $this->reader = $reader;
    $this->config = $config;
    $this->nameParser = new OptionNameParser($config);
    $argumentParser = new OptionArgumentParser($reader, $isAfterCommand);
    $this->builder = new OptionBuilder($argumentParser);
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
      $value = $this->builder->build($this->config[$name]);
    }
    $_ENV['context']->addOption($name, $value);
  }
}