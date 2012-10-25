<?php
class JingdongPropertyProductListProcessor {
  public function execute($path) {
    $status = 200;
    $replacementColumnList = array(
      'status' => $status,
      'version' => SPIDER_VERSION,
    );
    Db::bind('history', array(
      'processor' => 'ProductPropertyList', 'path' => $path,
    ), $replacementColumnList);
  }
}