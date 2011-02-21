<?php
class NewCommand {
  public function execute($type, $name) {
    $config = array();
    $processor = $config[$type];
    $processor->run($name);
    $color = $_ENV['context']->getOption('color');
  }
}