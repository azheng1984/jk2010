<?php
class TestCommand {
  public function execute($argument = null) {
    $GLOBALS['TEST_CALLBACK_TRACE'][] = array(
      'name' => __CLASS__.'->'.__FUNCTION__, 'argument' => $argument
    );
  }
}