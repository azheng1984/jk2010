<?php
class NotFoundScreen {
  public function render() {
    echo '404 Not Found page';
    //trigger_error($GLOBALS['UNHANDLED_EXCEPTION'], E_USER_ERROR);
  }
}