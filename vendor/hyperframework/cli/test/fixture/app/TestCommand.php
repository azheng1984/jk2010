<?php
class TestCommand {
  public function execute($argument = null) {
    $_ENV['callback'] = __CLASS__.'.'.__FUNCTION__;
    $_ENV['argument'] = $argument;
  }
}