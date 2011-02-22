<?php
class CommandContext {
  private $options = array ();

  public function getOption($name, $default = null, $isClassName = false) {
    if (!isset($this->options[$name])) {
      return $this->getDefaultOption($default, $isClassName);
    }
    return $this->options[$name];
  }

  public function addOption($name, $value) {
    $this->options[$name] = $value;
  }

  private function getDefaultOption($default, $isClassName) {
    if ($isClassName) {
      return new $default;;
    }
    return $default;
  }
}