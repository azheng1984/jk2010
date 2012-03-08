<?php
class HomeJson extends Json {
  protected function renderJson() {
    $path = '/';
    if (isset($GLOBALS['MERCHANT_TYPE'])) {
      $path = $GLOBALS['MERCHANT_TYPE']['path'];
    }
    $merchantAmount = $GLOBALS['HOME_CONFIG']['merchant_type_list'][$path][2];
    $buffer = array($merchantAmount);
    foreach ($GLOBALS['MERCHANT_LIST'] as $item) {
      $buffer[] = '["'.$item['name'].'","'.$item['uri']
        .'","'.$item['path'].'"]';
    }
    echo '[', implode(',', $buffer), ']';
  }
}