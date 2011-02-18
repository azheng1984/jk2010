<?php
class SyntaxErrorHandler {
  public function __construct() {
    file_put_contents('php://stderr', print_r($_SERVER, true));
    //print_r($_SERVER);
  }
}