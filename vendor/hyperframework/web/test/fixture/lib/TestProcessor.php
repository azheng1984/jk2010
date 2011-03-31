<?php
class TestProcessor {
  public function run($cache) {
    $_ENV['callback_argument'] = $cache;
    $_ENV['callback'] = __CLASS__.'->'.__FUNCTION__;
  }
}