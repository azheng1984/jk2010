<?php
class HomeJson extends Json {
  protected function renderJson() {
    $buffer = array();
    foreach ($GLOBALS['MERCHANT_LIST'] as $item) {
      $buffer[] = '["'.$item['name'].'","'.$item['uri']
        .'","'.$item['path'].'"]';
    }
    echo '[', implode(',', $buffer), ']';
  }
}