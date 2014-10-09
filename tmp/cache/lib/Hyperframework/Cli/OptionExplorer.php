<?php
namespace Hyperframework\Cli;

class OptionExplorer {
  public function render($name, $config) {
    ExplorerContext::getExplorer('Method')->render(
      $this->getNameList($name, $config), '__construct', $config
    );
    if (isset($config['description'])) {
      $this->renderDescription($config['description']);
    }
  }

  private function renderDescription($value) {
    $writer = ExplorerContext::getWriter();
    $writer->increaseIndentation();
    $writer->writeLine($value);
    $writer->decreaseIndentation();
    $writer->writeLine();
  }

  private function getNameList($name, $config) {
    $short = $this->getShorts($config);
    if (is_array($short)) {
      $short = implode(', -', $short);
    }
    if ($short !== null) {
      $short = ', -'.$short;
    }
    return '--'.$name.$short;
  }

  private function getShorts($config) {
    if (isset($config['short'])) {
      return $config['short'];
    }
  }
}
