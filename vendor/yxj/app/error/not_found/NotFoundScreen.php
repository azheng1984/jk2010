<?php
class NotFoundScreen {
  public function render() {
    echo '404 Not Found page';
    trigger_error($GLOBALS['EXCEPTION'], E_USER_ERROR);
  }
}