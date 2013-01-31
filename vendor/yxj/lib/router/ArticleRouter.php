<?php
class ArticleRouter {
  public static function execute($id) {
    $GLOBALS['ARTICLE_ID'] = $id;
    if (count($GLOBALS['PATH_SECTION_LIST']) === 3) {
      $GLOBALS['NAVIGATION_MODULE'] = 'browse';
      return '/article';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'edit') {
      $GLOBALS['NAVIGATION_MODULE'] = 'browse';
      return '/article/edit';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'new') {
      $GLOBALS['NAVIGATION_MODULE'] = 'browse';
      return '/article/new';
    }
  }
}