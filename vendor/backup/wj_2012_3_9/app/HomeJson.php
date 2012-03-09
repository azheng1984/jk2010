<?php
class HomeJson extends Json {
  protected function renderJson() {
    if (count($GLOBALS['MERCHANT_LIST']) === 0) {
      throw new NotFoundException;
    }
    $buffer = array($GLOBALS['MERCHANT_TYPE_CONFIG'][2]);
    foreach ($GLOBALS['MERCHANT_LIST'] as $item) {
      $buffer[] = '["'.$item['name'].'","'.$item['uri']
        .'","'.$item['path'].'"]';
    }
    echo '[', implode(',', $buffer), ']';
  }
}