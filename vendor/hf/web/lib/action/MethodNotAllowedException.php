<?php
class MethodNotAllowedException extends ApplicationException {
  public function __construct($methods, $message = '') {
    parent::__construct($message, '405 Method Not Allowed');
    header('Method: '.implode(';', $methods));
  }
}