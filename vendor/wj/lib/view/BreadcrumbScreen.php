<?php
class BreadcrumbScreen {
  public static function render() {
    $query = htmlentities($GLOBALS['URI']['QUERY'], ENT_QUOTES, 'utf-8');
    echo '<div id="h1_wrapper"><h1>';
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      echo '<a href="..">', $query, '</a> &rsaquo; '.$GLOBALS['URI']['CATEGORY']['name'];
    } else {
      echo $query;
    }
    echo '</h1></div>';
  }
}