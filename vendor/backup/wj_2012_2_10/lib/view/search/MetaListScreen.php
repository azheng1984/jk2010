<?php
class MetaListScreen {
  public static function getJs() {
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      //render brand list + same model
    } else {
      //render category list + same model
    }
    return 'meta_list=[];';
  }
}