<?php
class NewCommand {
  public function execute($arg1, $arg2 = null) {
    $color = $_ENV['context']->getOption('color');
  }
}