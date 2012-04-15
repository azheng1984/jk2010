<?php
class YoudaoPinyin {
  public static function getPinyin($word) {
    $pinyin = DbPinyin::get($word);
    if ($pinyin !== false) {
      return $pinyin;
    }
    $result = WebClient::get('dict.youdao.com', '/search?q='.urlencode($word));
    if ($result['content'] !== false) {
      $matches = null;
      preg_match(
        '{"phonetic">\[(.*?)\]</span>}', $result['content'], $matches
      );
      if (count($matches) > 1) {
        $pinyin = self::removeTone($matches[1]);
        DbPinyin::insert($word, $pinyin);
        return $pinyin;
      }
    }
  }

  private static function removeTone($pinyin) {
    $mapping = array(
      'ā' => 'a',
      'á' => 'a',
      'ǎ' => 'a',
      'à' => 'a',
      'ō' => 'o',
      'ó' => 'o',
      'ǒ' => 'o',
      'ò' => 'o',
      'ē' => 'e',
      'é' => 'e',
      'ě' => 'e',
      'è' => 'e',
      'ī' => 'i',
      'í' => 'i',
      'ǐ' => 'i',
      'ì' => 'i',
      'ū' => 'u',
      'ú' => 'u',
      'ǔ' => 'u',
      'ù' => 'u',
      'ǖ' => 'v',
      'ǚ' => 'v',
      'ǜ' => 'v',
      'ǘ' => 'v',
    );
    $matches = null;
    preg_match_all('/./u', $pinyin, $matches);
    $result = '';
    foreach ($matches[0] as $item) {
      if (isset($mapping[$item])) {
        $result .= $mapping[$item];
        continue;
      }
      $result .= $item;
    }
    return $result;
  }
}