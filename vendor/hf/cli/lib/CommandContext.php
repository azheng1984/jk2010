<?php
class CommandContext {
  private $options = array ();

  public function getOption($name, $default = null, $isClass = false) {
    if (!isset($this->options[$name])) {
      return $this->getDefaultOption($default, $isClass);
    }
    return $this->options[$name];
  }

  public function addOption($name, $value) {
    $this->options[$name] = $value;
  }

  private function getDefaultOption($default, $isClass) {
    if ($isClass) {
      return new $default;;
    }
    return $default;
  }
}