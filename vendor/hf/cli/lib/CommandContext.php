<?php
class CommandContext {
  private $options = array ();

  public function getOption($name, $default = null) {
    if (!isset($this->options[$name])) {
      return $default;
    }
    return $this->options[$name];
  }

  public function addOption($name, $value) {
    $this->options[$name] = $value;
  }
}