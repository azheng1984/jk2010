<?php
class SyntaxException extends Exception {
  private $config;

  public function __construct($message = null, $config = null) {
    parent::__construct($message);
    $this->config = $config;
  }

  public function __toString() {
    return $this->message."\n".$this->getUsage();
  }

  public function getUsage() {
    return print_r($this->config, true);
  }
}