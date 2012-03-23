<?php
class HomeJson extends Json {
  protected function renderJson() {
    if (count($GLOBALS['MERCHANT_SLIDE_LIST']) === 0) {
      throw new NotFoundException;
    }
    $buffer = array();
    foreach ($GLOBALS['MERCHANT_SLIDE_LIST'] as $item) {
      $uri = $item['uri_format'];
      $buffer[] = '["'.$item['name'].'","'.$uri
        .'","'.$item['path'].'","'.implode(' ', $item['slide_list']).'"]';
    }
    echo '[', implode(',', $buffer), ']';
  }
}