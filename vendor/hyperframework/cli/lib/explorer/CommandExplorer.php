<?php
class CommandExplorer {
  private $writer;

  public function __construct() {
    $this->writer = $_ENV['command_writer'];
  }

  public function render($name, $config) {
    if ($name !== null) {
      $methodExplorer = new MethodExplorer;
      $methodExplorer->render($name, $config);
      $this->writer->indent();
    }
    if (isset($config['description'])) {
      $this->writer->writeLine($config['description']);
      $this->writer->writeLine();
    }
    if (isset($config['option'])) {
      $this->renderOptionList($config['option']);
    }
    if ($name !== null) {
      $this->writer->indent(false);
    }
  }

  private function renderOptionList($config) {
    $this->writer->writeLine('[option]');
    $this->writer->indent();
    $optionExplorer = new OptionExplorer;
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        $name = $item;
        $item = array();
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $optionExplorer->render($name, $item);
    }
    $this->writer->indent(false);
  }
}