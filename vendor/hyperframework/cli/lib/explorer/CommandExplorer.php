<?php
class CommandExplorer {
  public function render($name, $config) {
    $writer = ExplorerContext::getWriter();
    if ($name !== null) {
      $this->renderMethod($name, $config);
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
    $optionExplorer = new OptionExplorer($_ENV['writer']);
    foreach ($config as $name => $item) {
      if (is_int($name)) {
        list($name, $item) = array($item, array());
      }
      if (!is_array($item)) {
        $item = array('class' => $item);
      }
      $this->renderOption($name, $item);
    }
    $writer->decreaseIndentation();
  }

  private function renderMethod($name, $config) {
    ExplorerContext::getExplorer('Method')->render(
      $name, 'execute', $config
    );
  }

  private function renderOption($name, $config) {
    ExplorerContext::getExplorer('Option')->render($name, $config);
  }
}