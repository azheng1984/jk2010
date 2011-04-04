<?php
class OptionExplorer {
  public function render($name, $config) {
    $this->renderMethod($name, $config);
    if (isset($config['description'])) {
      $this->renderDescription($config['description']);
    }
  }

  private function renderMethod($name, $config) {
    $_ENV['rendering_proxy']->render(
      'Method',
      array($this->getNameList($name, $config), '__construct', $config)
    );
  }

  private function renderDescription($value) {
    $writer = $_ENV['writer'];
    $writer->increaseIndentation();
    $writer->writeLine($value);
    $writer->decreaseIndentation();
    $writer->writeLine();
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