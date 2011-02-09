<?php
class CommandReader {
  private $inputArguments;
  private $inputArgumentLength;
  private $currentIndex = 1;
  
  public function __construct() {
    $this->inputArgumentLength = $_SERVER['argc'];
    $this->inputArguments = $_SERVER['argv'];
  }

  public function bindListener($name, $value) {
    
  }

  private function notify() {
    
  }

  public function expand($arguments) {
    array_splice($this->inputArguments, $this->currentIndex, 1, $arguments);
    $this->inputArgumentLength = count($this->inputArguments);
    --$this->currentIndex;
  }
}