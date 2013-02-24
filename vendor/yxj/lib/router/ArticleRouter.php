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
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'like') {
      $GLOBALS['NAVIGATION_MODULE'] = 'browse';
      return '/article/like';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'discussion') {
      $GLOBALS['NAVIGATION_MODULE'] = 'discussion';
      if ($GLOBALS['PATH_SECTION_LIST'][3] === 'new') {
        return '/discussion/topic/new';
      }
      if ($GLOBALS['PATH_SECTION_LIST'][3] !== '') {
        if ($GLOBALS['PATH_SECTION_LIST'][4] === 'new') {
          return '/discussion/topic/post/new';
        }
        return '/discussion/topic';
      }
      return '/discussion';
    }
    if ($GLOBALS['PATH_SECTION_LIST'][2] === 'user') {
      $GLOBALS['NAVIGATION_MODULE'] = 'user';
      return '/discussion/user';
    }
  }
}