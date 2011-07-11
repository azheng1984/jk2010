<?php
class SpiderCommand {
  public function execute($taskId = null) {
    if (Lock::execute() === false) {
      echo '[locked]'.PHP_EOL;
      return;
    }
    $spider = new Spider;
    $spider->execute();
  }
}