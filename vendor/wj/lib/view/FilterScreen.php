<?php
class FilterScreen {
  public static function render() {
    echo '<div id="filter"><h2>';
    if (!isset($GLOBALS['URI']['CATEGORY'])) {
      echo '<div id="breadcrumb">标签: <span class="first">分类</span></div>';
    } else {
      if ($this->key === false) {
        echo '<div id="breadcrumb">标签: <a class="first" href="">分类</a> &rsaquo; <span>',
          $this->category['name'].'</span></div>';
      } else {
        echo '<div id="breadcrumb">标签: <a class="first" href="">分类</a> &rsaquo;',
          ' <a href="'.'">'.$this->category['name'].'</a> &rsaquo; <span>'.$this->key['key'].'</span></div>';
      }
    }
    echo '</h2><ol></ol></div>';
  }
}