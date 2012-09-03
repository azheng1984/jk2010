<?php
class PublisherSignOutAction {
  public function GET() {
    setcookie('session_id', null);
    header('HTTP/1.1 302 Found');
    header('Location: /');
  }
}