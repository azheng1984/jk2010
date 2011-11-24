<?php
class SearchUriParser {
  public static function parse($sections) {
    array_shift($sections);
    array_pop($sections);
    $amount = count($sections);
    if ($amount === 0 || $sections[0] === '') {
      throw new NotFoundException;
    }
    $GLOBALS['URI'] = array();
    $GLOBALS['URI']['QUERY'] = urldecode($sections[0]);
    $result = '/'.$sections[0].'/';
    if ($amount > 1) {
      $GLOBALS['URI']['CATEGORY_NAME'] = urldecode($sections[1]);
      $result .= $sections[1].'/';
    }
    if ($amount > 2) {
      $GLOBALS['URI']['PROPERTIES'] = urldecode($sections[2]);
      $result .= $sections[2].'/';
    }
    $arguments = array();
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
      $GLOBALS['URI']['ID'] = $_GET['id'];
      $arguments[] = 'id='.$_GET['id'];
    }
    if (isset($_GET['sort'])) {
      $GLOBALS['URI']['SORT'] = $_GET['sort'];
      $arguments[] = 'sort='.$_GET['sort'];
    }
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $GLOBALS['URI']['PAGE'] = $_GET['page'];
      $arguments[] = 'page='.$_GET['page'];
    }
    if (count($arguments) > 0) {
      $result .= '?'.implode('&', $arguments);
    }
    return $result;
  }
}