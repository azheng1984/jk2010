<?php
class MerchantListUriParser {
  public static function parse() {
    if ($GLOBALS['URI']['PATH_SECTION_LIST'][1] !== '') {
      $GLOBALS['URI']['MERCHANT_INDEX_NAME']
        = $GLOBALS['URI']['PATH_SECTION_LIST'][1];
    }
    return '/';
  }
}