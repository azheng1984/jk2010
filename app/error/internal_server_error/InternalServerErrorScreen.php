<?php
class InternalServerErrorScreen {
  public function render() {
    header('Content-Type: text/plain');
    print_r(ErrorHandler::getException());
    echo 'previous output buffer: "'.ErrorHandler::getPreviousOutputBuffer().'"';
  }
}