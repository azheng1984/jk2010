<?php
class SignOutAction {
  public function GET() {
    session_destroy();
    $GLOBALS['APP']->redirect('http://dev.youxuanjia.com/');
  }
}