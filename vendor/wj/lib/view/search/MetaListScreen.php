<?php
class MetaListScreen {
  public static function render() {
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      //render brand list + same model
    } else {
      //render category list + same model
    }
    echo '<script type="text/javascript">meta_list=[];</script>';
  }
}