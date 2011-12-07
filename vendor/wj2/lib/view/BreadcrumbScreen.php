<?php
class BreadcrumbScreen {
  public static function render() {
    $query = htmlentities($GLOBALS['URI']['QUERY'], ENT_QUOTES, 'utf-8');
    echo '<div id="breadcrumb"><h1>';
    if (isset($GLOBALS['URI']['CATEGORY'])) {
      echo ' <a href="..">', $query, '</a> <img src="/bread_arrow.png" /> 分类: '.$GLOBALS['URI']['CATEGORY']['name'].'';
    } else {
      echo $query;
    }
    echo '</h1></div>';
  }
}