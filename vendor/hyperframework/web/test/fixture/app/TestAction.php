<?php
class TestAction {
  public function GET() {
    $_ENV['callback'] = __CLASS__.'.'.__FUNCTION__;
  }
}