<?php
class TestAction {
  public function GET() {
    $_ENV['callback'] = 'TestAction.GET';
  }
}