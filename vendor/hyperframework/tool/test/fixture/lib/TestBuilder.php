<?php
class TestBuilder {
  public function build($config = null) {
    $_ENV['callback'] = __CLASS__.'.'.__FUNCTION__;
    $_ENV['callback_argument'] = $config;
  }
}