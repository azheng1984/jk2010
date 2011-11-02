<?php
class Segmentation {
  public static function execute($text) {
    mmseg_load_chars('/home/wz/anjuke-php-mmseg-cdbfeb3/data/chars.dic');
    mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words-sogou.dic');
    mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words-custom.dic');
    mmseg_load_words('/home/wz/anjuke-php-mmseg-cdbfeb3/data/words.dic');
    $mmseg = mmseg_algor_create($text);
    $result = array();
    while (($token = mmseg_next_token($mmseg)) !== false) {
      $result[] = $token['text'];
    }
    mmseg_algor_destroy($mmseg);
    return $result;
  }
}