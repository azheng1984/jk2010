<?php
class PathBuilder {
  public function __construct() {
    $_GLOBAL['target'] = array(
      'category' => array(), 'product' => null, 'property' => array()
    );
  }

  private function getCategory($sections) {
   foreach ($sections as $section) {
      $result = false;
      if ($section !== '' && $this->analyze($section) === false) {
        break;
      }
    }
    $parentId = null;
    if ($this->category !== null) {
      $parentId = $this->category['id'];
    }
    $this->category = Category::get(urldecode($section), $parentId);
    if ($this->category !== null) {
      $_GLOBAL['target']['category'][] = $this->category;
      return true;
    }
    return false;
  }

  private function getProduct($sections) {
    
  }

  private function getProperty($sections) {
    
  }
}