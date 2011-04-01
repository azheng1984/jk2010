<?php
class TestScreen {
  public function render() {
    $_ENV['callback_trace'][] = __CLASS__.'->'.__FUNCTION__;
  }
}