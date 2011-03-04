<?php
class NewCliCommand {
  public function execute() {
    $generator = new ScaffoldGenerator;
    $generator->execute('web');
    echo "done\n";
  }
}