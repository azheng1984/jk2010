<?php
class TestException extends Exception {
  public function __toString() {
    return 'test_exception';
  }
}