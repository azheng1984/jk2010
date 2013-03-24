<?php
class InternalServerErrorScreen {
  public function render() {
    //echo '500 Internal Server Error';
    //var_dump($GLOBALS['EXCEPTION_HANDLER']->getException());
    trigger_error($GLOBALS['EXCEPTION_HANDLER']->getException(), E_USER_ERROR);
  }
}