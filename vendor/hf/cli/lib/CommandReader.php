<?php
class CommandReader {
  private $inputArguments;
  private $inputArgumentLength;
  private $currentIndex = 1;

  public function __construct() {
    $this->inputArgumentLength = $_SERVER['argc'];
    $this->inputArguments = $_SERVER['argv'];
  }

  public function get() {
    if ($this->currentIndex >= $this->inputArgumentLength) {
      return null;
    }
    return $this->inputArguments[$this->currentIndex];
  }

  public function move($step = 1) {
    $this->currentIndex += $step;
    return $this;
  }

  public function expand($arguments) {
    array_splice($this->inputArguments, $this->currentIndex, 1, $arguments);
    $this->inputArgumentLength = count($this->inputArguments);
    if ($this->currentIndex > 1) {
      $this->move(-1);
    }
  }
}