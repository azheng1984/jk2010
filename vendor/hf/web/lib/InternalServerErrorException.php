<?php
class InternalServerErrorException extends ApplicationException {
  public function __construct($message = null) {
    parent::__construct($message, '500 Internal Server Error');
  }
}