<?php
class MerchantListUriParser {
  public static function parse() {
    if (isset($_GET['q']) && $_GET['q'] !== ''
      && $GLOBALS['URI']['REQUEST_PATH'] === '/') {
      $GLOBALS['URI']['STANDARD_PATH'] = '/'.urlencode($_GET['q']).'/';
      return;
    }
    $GLOBALS['URI']['STANDARD_PATH'] = '/';
    return '/';
  }
}