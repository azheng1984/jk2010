<?php
class OptionExplorer {
  private $writer;

  public function __construct($writer) {
    $this->writer = $writer;
  }

  public function render($name, $config) {
    $this->renderMethod($name, $config);
    if (isset($config['description'])) {
      $this->writer->increaseIndentation();
      $this->writer->writeLine($config['description']);
      $this->writer->decreaseIndentation();
      $this->writer->writeLine();
    }
  }

  private function renderMethod($name, $config) {
    $methodExplorer = new MethodExplorer($this->writer);
    $methodExplorer->render(
      $this->getNameList($name, $config), '__construct', $config
    );
  }

  private function getNameList($name, $config) {
    $short = null;
    if (isset($config['short'])) {
      $short = $config['short'];
    }
    if (is_array($short)) {
      $short = implode(', -', $short);
    }
    if ($short !== null) {
      $short = ', -'.$short;
    }
    return '--'.$name.$short;
  }
}