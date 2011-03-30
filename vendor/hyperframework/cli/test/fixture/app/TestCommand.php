<?php
class TestCommand {
  public function execute() {
    $_ENV['callback'] = __CLASS__.'.'.__FUNCTION__;
  }
}