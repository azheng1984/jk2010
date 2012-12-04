<?php
class Segmentation {
  public static function initialize() {
    mmseg_load_chars('/home/azheng/wj_vendor/anjuke-php-mmseg-cdbfeb3/data/chars.dic');
    mmseg_load_words('/home/azheng/wj_vendor/anjuke-php-mmseg-cdbfeb3/data/words-sogou.dic');
    mmseg_load_words('/home/azheng/wj_vendor/anjuke-php-mmseg-cdbfeb3/data/words-custom.dic');
    //mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words.dic');
  }

  public static function execute($text) {
    $mmseg = mmseg_algor_create($text);
    $result = array();
    while (($token = mmseg_next_token($mmseg)) !== null) {
      $result[] = $token;
    }
    mmseg_algor_destroy($mmseg);
    return implode(' ', $result);
  }
}