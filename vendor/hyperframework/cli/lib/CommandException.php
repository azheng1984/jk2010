<?php
class CommandException extends Exception {
  public function __construct($message, $code = 1) {
    parent::__construct($message, $code);
  }

  public function __toString() {
    return $this->getMessage();
  }
}