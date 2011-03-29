<?php
class OptionExplorer {
  public function render($name, $config, $writer) {
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
    $methodExplorer = new MethodExplorer;
    $methodExplorer->render('--'.$name.$short, '__construct', $config, $writer);
    if (isset($config['description'])) {
      $writer->increaseIndentation();
      $writer->writeLine($config['description']);
      $writer->decreaseIndentation();
      $writer->writeLine();
    }
  }
}