<?php
class CommandView {
  private $writer;

  public function __construct() {
    $this->writer = $_ENV['command_writer'];
  }

  public function render($name, $config) {
    if ($name !== null) {
      $methodView = new MethodView;
      $methodView->render($name, $config);
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
    $optionView = new OptionView;
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        $name = $item;
        $item = array();
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $optionView->render($name, $item);
    }
    $this->writer->indent(false);
  }
}