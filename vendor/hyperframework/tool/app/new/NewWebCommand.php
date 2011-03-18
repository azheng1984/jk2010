<?php
class NewWebCommand {
  public function execute() {
    $generator = new ScaffoldGenerator;
    define('CLASS_LOADER_PATH', '/home/');
    $generator->generate('web');
  }
}