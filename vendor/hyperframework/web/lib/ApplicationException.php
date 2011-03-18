<?php
abstract class ApplicationException extends Exception {
  public function __construct($message, $statusCode) {
    parent::__construct($message);
    $this->code = $statusCode;
    header("HTTP/1.1 $statusCode");
  }
}