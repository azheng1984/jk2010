<?php
class NewWebCommand {
  public function execute() {
    $generator = new ScaffoldGenerator;
    $generator->execute('web');
    echo "done\n";
  }
}