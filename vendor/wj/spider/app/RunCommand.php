<?php
class RunCommand {
  public function execute($merchant) {
    Lock::execute();
    TaskCleaner::clean();
    $spider = new Spider;
    $spider->run();
  }
}