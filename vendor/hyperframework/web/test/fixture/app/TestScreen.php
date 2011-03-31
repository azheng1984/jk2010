<?php
class TestScreen {
  public function render() {
    $_ENV['callback'] = __CLASS__.'->'.__FUNCTION__;
  }
}