<?php
class CommandExplorer {
  public function render($name, $config, $writer) {
    if ($name !== null) {
      $methodExplorer = new MethodExplorer;
      $methodExplorer->render($name, 'execute', $config, $writer);
      $writer->increaseIndentation();
    }
    if (isset($config['description'])) {
      $writer->writeLine($config['description']);
      $writer->writeLine();
    }
    if (isset($config['option'])) {
      $this->renderOptionList($config['option'], $writer);
    }
    if ($name !== null) {
      $writer->decreaseIndentation();
    }
  }

  private function renderOptionList($config, $writer) {
    $writer->writeLine('[option]');
    $writer->increaseIndentation();
    $optionExplorer = new OptionExplorer;
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        list($name, $item) = array($item, array());
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $optionExplorer->render($name, $item);
    }
    $writer->decreaseIndentation();
  }
}