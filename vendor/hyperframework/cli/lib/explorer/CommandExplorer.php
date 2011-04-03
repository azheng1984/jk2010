<?php
class CommandExplorer {
  private $writer;

  public function __construct($writer) {
    $this->writer = $writer;
  }

  public function render($name, $config) {
    if ($name !== null) {
      $this->renderMethod($name, $config);
      $this->writer->increaseIndentation();
    }
    if (isset($config['description'])) {
      $this->writer->writeLine($config['description']);
      $this->writer->writeLine();
    }
    if (isset($config['option'])) {
      $this->renderOptionList($config['option']);
    }
    if ($name !== null) {
      $this->writer->decreaseIndentation();
    }
  }

  private function renderMethod($name, $config) {
    $methodExplorer = new MethodExplorer($this->writer);
    $methodExplorer->render($name, 'execute', $config);
  }

  private function renderOptionList($config) {
    $this->writer->writeLine('[option]');
    $this->writer->increaseIndentation();
    $optionExplorer = new OptionExplorer($this->writer);
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        list($name, $item) = array($item, array());
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $optionExplorer->render($name, $item);
    }
    $this->writer->decreaseIndentation();
  }
}