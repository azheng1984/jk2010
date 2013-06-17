<?php
class UnsupportedMediaTypeException extends ApplicationException {
  public function __construct($message = null, $previous = null) {
    parent::__construct($message, '415 Unsupported Media Type', $previous);
  }
}