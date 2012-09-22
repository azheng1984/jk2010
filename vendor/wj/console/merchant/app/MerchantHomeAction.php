<?php
class MerchantHomeAction {
  public function GET() {}

  public function POST() {
    $this->signIn();
  }

  private function signIn() {
    setcookie('session_id', 1);
    $_COOKIE['session_id'] = 1;
  }
}