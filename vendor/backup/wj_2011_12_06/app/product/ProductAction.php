<?php
class ProductAction {
  public function GET() {
    header('HTTP/1.1 302 Found');
    echo 'product';
  }
}