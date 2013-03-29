<?php
class InternalServerErrorScreen {
  public function render() {
    if ($GLOBALS['EXCEPTION_HANDLER']->getException() === null) {
      throw new NotFoundException;
    }
    echo '<h1>500 Internal Server Error</h1><pre>';
    echo $GLOBALS['EXCEPTION_HANDLER']->getException();
    echo '</pre>';
  }
}