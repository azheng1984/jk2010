<?php
class RunTaskCommand {
  public function execute() {
    if (Lock::execute() === false) {
      echo '[locked]'.PHP_EOL;
      return;
    }
    $spider = new Spider;
    $spider->run();
  }
}