<?php
class CommandExplorer {
  public function render($name, $config) {
    $writer = ExplorerContext::getWriter();
    if ($name !== null) {
      ExplorerContext::getExplorer('Method')->render(
        $name, 'execute', $config
      );
      $writer->increaseIndentation();
    }
    if (isset($config['description'])) {
      $writer->writeLine($config['description']);
      $writer->writeLine();
    }
    if (isset($config['option'])) {
      $this->renderOptionList($config['option']);
    }
    if ($name !== null) {
      $writer->decreaseIndentation();
    }
  }

  private function renderOptionList($config) {
    $writer = ExplorerContext::getWriter();
    $writer->writeLine('[option]');
    $writer->increaseIndentation();
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        list($name, $item) = array($item, array());
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      ExplorerContext::getExplorer('Option')->render($name, $item);
    }
    $writer->decreaseIndentation();
  }
}