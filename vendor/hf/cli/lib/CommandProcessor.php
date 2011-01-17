<?php
class CommandProcessor {
  private $argc;
  private $argv;

  public function __construct($argc, $argv) {
    $this->argc = $argc;
    $this->argv = $argv;
  }

  public function run($name) {
    require_once ROOT_PATH."app/new/$name.php";
    $command = new $name;
    $command->run($this->argc, $this->argv);
  }
}