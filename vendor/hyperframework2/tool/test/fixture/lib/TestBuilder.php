<?php
class TestBuilder {
  public function build($argument = null) {
    $GLOBALS['TEST_CALLBACK_TRACE'][] = array(
      'method' => __CLASS__.'->'.__FUNCTION__, 'argument' => $argument
    );
    return new TestCache;
  }
}