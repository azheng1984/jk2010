<?php
class TestAction {
  public function GET() {
    $_ENV['callback_trace'][] = __CLASS__.'->'.__FUNCTION__;
  }
}