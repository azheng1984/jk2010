<?php
class SyntaxException extends Exception {
  public function __toString() {
    return parent::__toString();
  }
}