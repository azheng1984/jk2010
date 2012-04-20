<?php
class AlphabetIndex {
  public static function get($segment, $pinyin) {
    $firstPinyin = substr($pinyin, 0 , 1);
    $commaPosition = strpos($pinyin, ',');
    $spacePosition = strpos($pinyin, ' ');
    if ($commaPosition !== false 
      && ($spacePosition === false || $spacePosition > $commaPosition)) {
      $list = explode(' ', $segment);
      $tmp = YoudaoPinyin::getPinyin($list[0]);
      if ($tmp !== null) {
        $firstPinyin = substr($tmp, 0 , 1);
      }
    }
    $alphabetIndex = 0;
    $ascii = ord($firstPinyin);
    if ($ascii > 96 && $ascii < 123) {
      $alphabetIndex = $ascii - 32;
    }
    if ($ascii > 47 && $ascii < 58) {
      $alphabetIndex = 48;
    }
    return $alphabetIndex;
  }
}