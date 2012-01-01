<?php
class Segmentation {
  private static $initialized;

  private static function initialize() {
    if (self::$initialized) {
      return;
    }
    mmseg_load_chars('/home/wz/anjuke-php-mmseg-cdbfeb3/data/chars.dic');
    mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words-sogou.dic');
    mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words-custom.dic');
    //mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words.dic');
    self::$initialized = true;
  }

  public static function execute($text) {
    self::initialize();
    $mmseg = mmseg_algor_create($text);
    $result = array();
    while (($token = mmseg_next_token($mmseg)) !== null) {
      $result[] = $token['text'];
    }
    mmseg_algor_destroy($mmseg);
    return $result;
  }
}