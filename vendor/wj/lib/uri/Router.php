<?php
class Router {
  public function execute() {
    $GLOBALS['URI'] = array();
    $app = $this->dispatch();
    if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
      $GLOBALS['URI']['STANDARD_PATH'] = 'http://'.$_SERVER['SERVER_NAME']
        .$GLOBALS['URI']['STANDARD_PATH'];
    }
    if ($GLOBALS['URI']['REQUEST_PATH'] !== $GLOBALS['URI']['STANDARD_PATH']) {
      $this->setLocationHeader();
      $app = '/redirect';
    }
    return $app;
  }

  private function dispatch() {
    $GLOBALS['URI']['REQUEST_PATH'] = $_SERVER['REQUEST_URI'];
    $questionMarkposition = strpos($_SERVER['REQUEST_URI'], '?');
    if ($questionMarkposition !== false) {
      $GLOBALS['URI']['REQUEST_PATH'] = substr(
        $_SERVER['REQUEST_URI'], 0, $questionMarkposition
      );
    }
    if ($GLOBALS['URI']['REQUEST_PATH'] === '/') {
      return MerchantListUriParser::parse();
    }
    $GLOBALS['URI']['PATH_SECTION_LIST'] = explode(
      '/', $GLOBALS['URI']['REQUEST_PATH']
    );
    if ($GLOBALS['URI']['PATH_SECTION_LIST'][1] === '+i') {
      return SitemapUriParser::parse();
    }
    return SearchUriParser::parse();
  }

  private function setLocationHeader() {
    $location = $GLOBALS['URI']['STANDARD_PATH'];
    if ($_SERVER['QUERY_STRING'] !== '') {
      $location .= '?'.$_SERVER['QUERY_STRING'];
    }
    header('Location: '.$location);
  }
}