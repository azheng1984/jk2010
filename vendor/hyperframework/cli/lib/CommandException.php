<?php
class CommandException extends Exception {
  public function __toString() {
    return $this->message.PHP_EOL;
  }
}