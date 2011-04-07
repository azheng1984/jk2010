<?php
class TestCommand {
  private $options;

  public function __construct($options) {
    $this->options = $options;
  }

  public function execute($argument = null) {
    $GLOBALS['TEST_CALLBACK_TRACE'][] = array(
      'name' => __CLASS__.'->'.__FUNCTION__,
      'argument' => $argument,
      'option' => $this->options,
    );
  }
}