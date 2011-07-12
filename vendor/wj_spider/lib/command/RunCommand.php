<?php
class RunCommand {
  public function execute() {
    Lock::execute();
    $spider = new Spider;
    $spider->run();
  }
}