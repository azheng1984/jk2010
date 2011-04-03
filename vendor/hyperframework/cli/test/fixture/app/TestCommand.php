<?php
class TestCommand {
  public function execute($argument = null) {
    $_ENV['callback_trace'][] = array(
      'name' => __CLASS__.'.'.__FUNCTION__, 'argument' => $argument
    );
  }
}