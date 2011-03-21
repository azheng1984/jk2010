<?php
class ViewBuilder {
  public function build($fileName) {
    $cache = array();
    $types = require(
      CACHE_PATH.'cache_builder'.DIRECTORY_SEPARATOR.'view.config.php'
    );
    foreach ($types as $type) {
      if (substr($fileName, -strlen($type)) === "$type.php") {
        $cache[$type] = preg_replace('/.php$/', '', $fileName);
      }
    }
    return $cache;
  }
}