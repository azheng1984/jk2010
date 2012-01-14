<?php
class Router {
  public function execute() {
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      header(
        'Location: http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']
      );
      return '/redirect';
    }
    $GLOBALS['URI'] = array('REQUEST_PATH' => $_SERVER['REQUEST_URI']);
    $queryStringPosition = strpos($_SERVER['REQUEST_URI'], '?');
    if ($queryStringPosition !== false) {
      $GLOBALS['URI']['REQUEST_PATH'] = substr(
        $_SERVER['REQUEST_URI'], 0, $queryStringPosition
      );
    }
    if (isset($_GET['q']) && $_GET['q'] !== ''
      && $GLOBALS['URI']['REQUEST_PATH'] === '/') {
      header('Location: /'.urlencode($_GET['q']).'/');
      return '/redirect';
    }
    $GLOBALS['URI']['PATH_SECTION_LIST'] = explode(
      '/', $GLOBALS['URI']['REQUEST_PATH']
    );
    if (!isset($GLOBALS['URI']['PATH_SECTION_LIST'][2])) {
      return MerchantListUriParser::parse();
    }
    if ($GLOBALS['URI']['PATH_SECTION_LIST'][1] === '+i') {
      return IndexUriParser::parse();
    }
    return SearchUriParser::parse();
  }
}