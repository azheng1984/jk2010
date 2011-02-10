<?php
class UnsupportedMediaTypeException extends ApplicationException {
  public function __construct($message = '') {
    parent::__construct($message, '415 Unsupported Media Type');
  }
}