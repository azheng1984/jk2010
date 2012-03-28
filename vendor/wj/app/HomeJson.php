<?php
class HomeJson extends Json {
  protected function renderJson() {
//     if (count($GLOBALS['SLIDESHOW']) === 0) {
//       throw new NotFoundException;
//     }
    $list = array();
    foreach ($GLOBALS['SLIDESHOW'] as $item) {
      $list[] = '["'.$item['name'].'","'.$item['uri_format'].'","'
      .$item['path'].'",["'.implode('","', $item['slide_list']).'"]]';
    }
    echo '[', implode(',', $list), ']';
  }
}