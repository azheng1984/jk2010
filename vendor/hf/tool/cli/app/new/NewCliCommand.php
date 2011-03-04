<?php
class NewCliCommand {
  public function execute() {
    $generator = new ScaffoldGenerator;
    $generator->execute('cli');
    echo "done\n";
  }
}