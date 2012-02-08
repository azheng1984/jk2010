<?php
class ProductRecognitionUriParser {
  public static function parse() {
    $GLOBALS['URI']['PRODUCT_RECOGNITION_ID']
      = $GLOBALS['URI']['PATH_SECTION_LIST'][2];
    return 'search';
  }
}