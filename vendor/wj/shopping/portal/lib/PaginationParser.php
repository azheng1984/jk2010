<?php
class PaginationParser {
  public static function parseGet() {
    if (isset($_GET['page']) === false
      || is_numeric($_GET['page']) === false
      || $_GET['page'] < 1) {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    $GLOBALS['PAGE'] = intval($_GET['page']);
  }

  public static function parsePath($path) {
    if ($path === '') {
      $GLOBALS['PAGE'] = 1;
      return;
    }
    if (is_numeric($path) === false || $path < 2) {
      throw new NotFoundException;
    }
    $GLOBALS['PAGE'] = intval($path);
  }
}