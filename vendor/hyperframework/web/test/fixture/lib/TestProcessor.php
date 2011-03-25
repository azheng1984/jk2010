<?php
class TestProcessor {
  public function run($cache) {
    $_ENV['cache_data'] = $cache;
    $_ENV['callback'] = 'TestProcessor.run';
  }
}