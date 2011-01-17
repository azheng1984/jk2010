<?php
class NotFoundException extends ApplicationException {
  public function __construct($message = '') {
    parent::__construct($message, '404 Not Found');
  }
}