<?php
class NotFoundScreen {
  public function render() {
    throw new Exception('hi');
    echo '404 Not Found page';
    //trigger_error($GLOBALS['UNHANDLED_EXCEPTION'], E_USER_ERROR);
  }
}