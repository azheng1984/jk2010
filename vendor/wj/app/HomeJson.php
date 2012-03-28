<?php
class HomeJson extends Json {
  protected function renderJson() {
    if (count($GLOBALS['SLIDESHOW']) === 0) {
      throw new NotFoundException;
    }
    echo '[["京东商城0","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城1","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城2","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城3","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]],'
      .'["京东商城4","www.360buy.com/?source=huobiwanjia","360buy",["www.360buy0.com","www.360buy1.com", "www.360buy2.com", "www.360buy3.com", "www.360buy4.com"]]]';
    return;
    $buffer = array();
    foreach ($GLOBALS['SLIDE_LIST'] as $item) {
      $uri = $item['uri_format'];
      $buffer[] = '["'.$item['name'].'","'.$uri
        .'","'.$item['path'].'","'.implode(' ', $item['slide_list']).'"]';
    }
    echo '[', implode(',', $buffer), ']';
  }
}