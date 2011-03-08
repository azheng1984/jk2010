<?php
class NewCliCommand {
  public function execute($name) {
    $generator = new ScaffoldGenerator;
    define('APP_NAME', $name);
    define('CLASS_LOADER_PATH', '/home/');
    $generator->generate('cli');
    echo "done\n";
  }
}