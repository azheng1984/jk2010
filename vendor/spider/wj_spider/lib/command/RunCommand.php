<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    TaskCleaner::clean();
    $spider = new Spider;
    $spider->run();
  }
}