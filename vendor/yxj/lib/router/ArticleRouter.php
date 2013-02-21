<?php
class ArticleRouter {
  public static function execute($id) {
    $GLOBALS['ARTICLE_ID'] = $id;
    if (count($GLOBALS['PATH_SECTION_LIST']) === 3
      && $GLOBALS['PATH_SECTION_LIST'][2] === '') {
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
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'discussion') {
      $GLOBALS['NAVIGATION_MODULE'] = 'discussion';
      return '/article/discussion';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'member') {
      $GLOBALS['NAVIGATION_MODULE'] = 'member';
      return '/article/member';
    }
  }
}