<?php
class CommandContext {
  private $options = array ();

  public function getOption($name, $isNullable = false) {
    if (!isset($this->options[$name])) {
      if ($isNullable) {
        throw new Exception;
      }
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