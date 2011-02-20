<?php
class CommandContext {
  private $options = array ();

  public function getOption($name, $isNullable = false) {
    if (!isset($this->options[$name])) {
      if ($isNullable) {
        throw new SyntaxException;
      }
      return null;
    }
    return $this->options[$name];
  }

  public function addOption($name, $value) {
    $this->options[$name] = $value;
  }
}