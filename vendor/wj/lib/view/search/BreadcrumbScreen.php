<?php
class BreadcrumbScreen {
  public static function render() {
    $query = htmlentities($GLOBALS['URI']['QUERY'], ENT_QUOTES, 'utf-8');
    $buffer = '';
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      $buffer = ' <span><a href="..">'.$query.'</a></span>分类: '
        .$GLOBALS['URI']['CATEGORY']['name'];
    } else {
      $buffer = $query;
    }
    echo '<div id="breadcrumb"><h1>', $buffer, '</h1></div>';
  }
}