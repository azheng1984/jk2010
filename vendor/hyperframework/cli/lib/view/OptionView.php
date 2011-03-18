<?php
class OptionView {
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
    $methodView = new MethodView;
    $methodView->render('--'.$name.$short, $config);
    if (isset($config['description'])) {
      $writter->indent();
      $writter->writeLine($config['description']);
      $writter->writeLine();
      $writter->indent(false);
    }
  }
}