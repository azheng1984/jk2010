<?php
class CommandReader {
  private $arguments;
  private $length;
  private $index;

  public function __construct() {
    $this->length = $_SERVER['argc'];
    $this->arguments = $_SERVER['argv'];
  }

  public function get() {
    if ($this->index < 1) {
      $this->index = 1;
    }
    if ($this->index >= $this->length) {
      return null;
    }
    return $this->arguments[$this->index];
  }

  public function move($isForward = true) {
    $this->index += $isForward ? 1 : -1;
    return $this;
  }

  public function expand($arguments) {
    array_splice($this->arguments, $this->index, 1, $arguments);
    $this->length = count($this->arguments);
    $this->move(false);
  }
}