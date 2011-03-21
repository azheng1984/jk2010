<?php
class ViewBuilder {
  private $types;

  public function __construct($types) {
    $this->types = $types;
  }
  
  public function build($fileName) {
    $cache = array();
    foreach ($this->types as $type) {
      $postfix = "$type.php";
      if (substr($fileName, -strlen($postfix)) === $postfix) {
        $cache[$type] = preg_replace('/.php$/', '', $fileName);
      }
    }
    return $cache;
  }
}