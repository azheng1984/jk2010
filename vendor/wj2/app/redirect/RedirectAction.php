<?php
class RedirectAction {
  public function GET() {
    header('HTTP/1.1 301 Moved Permanently');
  }
}