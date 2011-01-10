<?php
abstract class ApplicationException extends Exception {
  public function __construct($message, $code) {
    parent::__construct($message);
    $this->code = $code;
    header("HTTP/1.1 {$code}");
  }
}