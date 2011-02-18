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
    $configPath = HF_CONFIG_PATH.'cli'
                 .DIRECTORY_SEPARATOR.__CLASS__.'.config.php';
    $this->readConfig(require $configPath);
  }

  public function run() {
    while (($item = $this->reader->get()) !== null) {
      $this->parse($item);
      $this->reader->move();
    }
    $this->executeCommand();
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
    $this->readConfig($this->config['command'][$item]);
    $this->isAllowOption = true;
  }

  private function executeCommand() {
    if (!isset($this->config['class'])) {
      throw new SyntaxException;
    }
    $reflector = new ReflectionMethod($this->config['class'], 'execute');
    $verifier = new ArgumentVerifier;
    $length = count($this->arguments);
    $isInfinite = in_array('infinite_argument', $this->config, true);
    $verifier->run($reflector, $length, $isInfinite);
    $reflector->invokeArgs(new $this->config['class'], $this->arguments);
  }

  private function readConfig($source) {
    if (!is_array($source)) {
      $source = array('class' => $source, 'option' => array());
    }
    $this->isAfterCommand = isset($source['class']);
    $this->optionParser = new OptionParser($this->reader,
                                           $source['option'],
                                           $this->isAfterCommand);
    $this->config = $source;
  }
}