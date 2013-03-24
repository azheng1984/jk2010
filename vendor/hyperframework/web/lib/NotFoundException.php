<?php
class NotFoundException extends ApplicationException {
  public function __construct($message = null, $previous = null) {
    parent::__construct($message, '404 Not Found', $previous);
  }
}