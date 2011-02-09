<?php
class CommandContext {
  private $options = array();

  public function getOption($name, $isNullable = false) {
    if (!isset($this->options[$name]) && !$isNullable) {
      throw new Exception;
    }
    if (!isset($this->options[$name])) {
      return null;
    }
    return $this->options[$name];
  }

  public function addOption($name, $value) {
    $this->options[$name] = $value;
  }

  public function read() {
    return 'hi';
  }

  public function write($string) {
    echo $string;
  }
}