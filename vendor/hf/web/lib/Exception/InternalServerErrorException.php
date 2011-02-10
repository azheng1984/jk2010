<?php
class InternalServerErrorException extends ApplicationException {
  public function __construct($message = '') {
    parent::__construct($message, '500 Internal Server Error');
  }
}