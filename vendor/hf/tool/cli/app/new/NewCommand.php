<?php
class NewCommand {
  public function run($argc, $argv) {
    $generator = new ScaffoldGenerator;
    $generator->run('Jiakr', getcwd());
  }
}