<?php

class CategoryPathParser {
  public function execute($sections) {
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
}