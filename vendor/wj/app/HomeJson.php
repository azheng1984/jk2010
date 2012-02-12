<?php
class HomeJson extends Json {
  protected function renderJson() {
    $buffer = array();
    foreach ($GLOBALS['MERCHANT_LIST'] as $item) {
      $buffer[] = '{"name":"'.$item['name'].'","uri":"'.$item['uri']
        .'","path":"'.$item['path'].'"}';
    }
    echo '[', implode(',', $buffer), ']';
  }
}