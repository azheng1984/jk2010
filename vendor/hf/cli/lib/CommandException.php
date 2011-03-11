<?php
class CommandException extends Exception {
  public function __construct($message = null) {
    parent::__construct($message);
  }

  public function __toString() {
    return $this->message."\n";
  }
}