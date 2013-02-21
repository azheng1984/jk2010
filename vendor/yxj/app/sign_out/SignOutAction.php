<?php
class SignOutAction {
  public function GET() {
    session_destroy();
    header('HTTP/1.1 302 Found');
    header('Location: http://dev.youxuanji.com/');
  }
}