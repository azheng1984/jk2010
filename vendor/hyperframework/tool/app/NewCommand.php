<?php
class NewCommand {
  public function execute($type) {
    $generator = new ScaffoldGenerator;
    define('CLASS_LOADER_PATH', '/home/');
    $generator->generate($type);
  }
}