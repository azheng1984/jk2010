<?php
class NotFoundScreen {
  public function render() {
    if ($GLOBALS['EXCEPTION_HANDLER']->getException() === null) {
      throw new NotFoundException;
    }
    echo '<h1>404 Not Found page</h1>';
    //trigger_error($GLOBALS['UNHANDLED_EXCEPTION'], E_USER_ERROR);
  }
}