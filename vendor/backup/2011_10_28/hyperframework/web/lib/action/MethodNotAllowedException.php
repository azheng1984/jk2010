<?php
class MethodNotAllowedException extends ApplicationException {
  public function __construct($methods, $message = null) {
    parent::__construct($message, '405 Method Not Allowed');
    header('Allow: '.implode(', ', $methods));
  }
}