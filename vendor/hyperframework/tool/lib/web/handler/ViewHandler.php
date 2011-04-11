<?php
class ViewHandler {
  private $types;

  public function __construct($types = array()) {
    $this->types = $types;
  }

  public function execute($fileName) {
    $cache = null;
    foreach ($this->types as $type) {
      $postfix = $type.'.php';
      if (substr($fileName, -strlen($postfix)) === $postfix) {
        $cache = array($type => preg_replace('/.php$/', '', $fileName)); //?
      }
    }
    return $cache;
  }
}