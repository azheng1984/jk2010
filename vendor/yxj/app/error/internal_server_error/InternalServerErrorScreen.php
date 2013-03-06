<?php
class InternalServerErrorScreen {
  public function render() {
    echo '500 Internal Server Error';
    trigger_error($GLOBALS['UNHANDLED_EXCEPTION'], E_USER_ERROR);//log error
  }
}