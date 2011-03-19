<?php
class OptionExplorer {
  public function render($name, $config) {
    $writter = $_ENV['command_writer'];
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
    $methodExplorer->render('--'.$name.$short, $config);
    if (isset($config['description'])) {
      $writter->increaseIndentation();
      $writter->writeLine($config['description']);
      $writter->writeLine();
      $writter->decreaseIndentation();
    }
  }
}