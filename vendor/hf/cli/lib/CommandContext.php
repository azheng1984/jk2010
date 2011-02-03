<?php
class CommandContext {
  public function getOption($name, $isNullable = false) {
    return true;
  }
  
  public function addOption($name, $value) {
    
  }

  public function read() {
    return 'hi';
  }

  public function write($string) {
    echo $string;
  }
}