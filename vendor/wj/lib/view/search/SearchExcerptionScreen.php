<?php
class SearchExcerptionScreen {
  private static $keywordList;
  private static $matchList;

  public static function initialize($keywordList) {
    self::$keywordList = array();
    foreach ($keywordList as $item) {
      self::$keywordList[] = array($item, 0);
    }
  }

  public static function excerpt($linkList, $textList, $text) {
    $list = self::getList($linkList, $textList, $text);
    $linkSection = '';
    if (count($list['link']) !== 0) {
      foreach ($list['link'] as $item) {
        $linkSection .= self::renderLink($item);
      }
    }
    if ($list['is_cut']) {
      array_push($list['text'], array('…', false));
    }
    $textSection = '';
    if (count($list['text']) !== 0) {
      foreach ($list['text'] as $item) {
        $textSection .= self::renderText($item);
      }
    }
    if ($textSection !== '') {
      $textSection =
        str_replace('； ', '; ', str_replace('： ', ': ', $textSection));
    }
    $result = $linkSection.$textSection;
    if ($result === '') {
      return;
    }
    return '<ul>'.$result.'</ul>';
  }

  private static function getList($linkList, $textList, $text) {
    $listAmount = count($linkList) + count($textList);
    $result = array('link' => array(), 'text' => array());
    foreach ($linkList as $item) {
      $result['link'][] = array($item, false);
    }
    foreach ($textList as $item) {
      $result['text'][] = array($item, false);
    }
    self::$matchList = array();
    $originalList = explode("\n", $text);
    $originalAmount = count($originalList);
    self::buildMatchList(
      $originalList, $originalAmount, self::$keywordList, $listAmount
    );
    $noMatchList = array('link' => array(), 'text' => array());
    $originalPropertyAmount = $listAmount;
    $type = isset($GLOBALS['CATEGORY']) ? 'link' : 'text';
    for ($index = 0; $index < $originalAmount; ++$index) {
      $propertyText = $originalList[$index];
      if ($propertyText === '') {
        $type = 'text';
        continue;
      }
      ++$originalPropertyAmount;
      if ($listAmount === 8) {
        continue;
      }
      if (isset(self::$matchList[$index])) {
        $result[$type][] = array($propertyText, self::$matchList[$index]);
        ++$listAmount;
        continue;
      }
      $noMatchList[$type][] = array($propertyText, null);
    }
    $result['is_cut'] = $originalPropertyAmount > 8;
    if ($listAmount === 8) {
      return $result;
    }
    foreach ($noMatchList['link'] as $item) {
      $result['link'][] = $item;
      ++$listAmount;
      if ($listAmount === 8) {
        return $result;
      }
    }
    foreach ($noMatchList['text'] as $item) {
      $result['text'][] = $item;
      ++$listAmount;
      if ($listAmount === 8) {
        return $result;
      }
    }
    return $result;
  }

  private static function buildMatchList(
    $list, $amount, $keywordList, $resultAmount
  ) {
    $matchKeywordList = array();
    foreach ($keywordList as $item) {
      $keyword = $item[0];
      if ($resultAmount === 8) {
        return;
      }
      for ($index = $item[1]; $index < $amount; ++$index) {
        $propertyText = $list[$index];
        if ($propertyText === '') {
          continue;
        }
        if (strpos($propertyText, $keyword) === false) {
          continue;
        }
        self::$matchList[$index] = $keyword;
        ++$resultAmount;
        $list[$index] = '';
        if (++$index < $amount) {
          $matchKeywordList[] = array($keyword, $index);
        }
        break;
      }
    }
    if ($resultAmount < 8 && count($matchKeywordList) !== 0) {
      self::buildMatchList($list, $amount, $matchKeywordList, $resultAmount);
    }
  }

  private static function renderLink($item) {
    list($text, $keyword) = $item;
    if ($keyword === false) {
      return '<li>'.$text.'</li>';
    }
    $list = explode('： ', $text, 2);
    if (count($list) !== 2) {
      return;
    }
    $length = mb_strlen($text, 'UTF-8');
    $isCut = $length > 32;
    list($keyName, $valueName) = $list;
    if (isset($GLOBALS['PROPERTY_LIST'][$keyName]) === false
      && $isCut === false) {
      return self::mergeLink($keyName, explode('； ', $valueName));
    }
    if (strpos($valueName, '； ') === false) {
      return isset(
          $GLOBALS['PROPERTY_LIST'][$keyName]['value_list'][$valueName]
      ) ? null : self::mergeLink($keyName, array($valueName));
    }
    $valueNameList = array();
    foreach (explode('； ', $valueName) as $item) {
      if (isset($GLOBALS['PROPERTY_LIST'][$keyName]['value_list'][$item])) {
        continue;
      }
      $valueNameList[] = $item;
    }
    if (count($valueNameList) === 0) {
      return;
    }
    if ($isCut) {
      $cutLength = 30 - mb_strlen($keyName, 'UTF-8');
      $valueNameList = self::cutLink($valueNameList, $keyword, $cutLength);
      $valueNameList[] = '…';
    }
    return self::mergeLink($keyName, $valueNameList);
  }

  private static function mergeLink($keyName, $valueNameList) {
    $result = '<li>'.$keyName.': <span class="value">'
      .implode('</span>; <span class="value">', $valueNameList).'</span></li>';
    return str_replace('<span class="value">…</span>', '…', $result);
  }

  private static function cutLink($valueNameList, $keyword, $length) {
    $matchList = array();
    $noMatchList = array();
    foreach ($valueNameList as $valueName) {
      if (strpos($valueName, $keyword) !== false) {
        $matchList[] = $valueName;
        continue;
      }
      $noMatchList[] = $valueName;
    }
    $result = array();
    foreach ($matchList as $item) {
      $length -= mb_strlen($item, 'UTF-8');
      if ($length < 0 && count($result) !== 0) {
        return $result;
      }
      $result[] = $item;
    }
    foreach ($noMatchList as $item) {
      $length -= mb_strlen($item, 'UTF-8');
      if ($length < 0 && count($result) !== 0) {
        return $result;
      }
      $result[] = $item;
    }
    return $result;
  }

  private static function renderText($item) {
    list($text, $keyword) = $item;
    if ($keyword === false) {
      return '<li>'.$text.'</li>';
    }
    $length = mb_strlen($text, 'UTF-8');
    if ($length < 32) {
      return '<li>'.$text.'</li>';
    }
    $list = explode('： ', $text, 2);
    if (count($list) !== 2) {
      return;
    }
    list($keyName, $valueName) = $list;
    $cutLength = 30 - mb_strlen($keyName, 'UTF-8');
    return '<li>'.$keyName.': '
      .self::cutText($valueName, $keyword, $cutLength).'</li>';
  }

  private static function cutText($valueName, $keyword, $length) {
    if ($length < 16) {
      $length = 16;
    }
    $keywordLength = mb_strlen($keyword, 'UTF-8');
    if ($keywordLength > $length - 8) {
      $length = $keywordLength + 8;
    }
    $orignalLength = mb_strlen($valueName, 'UTF-8');
    if ($length > $orignalLength) {
      return $valueName;
    }
    if ($keyword === null) {
      return mb_substr($valueName, 0, $length, 'UTF-8').'…';
    }
    $start = mb_strpos($valueName, $keyword, 0, 'UTF-8');
    if ($start === false) {
      return mb_substr($valueName, 0, $length, 'UTF-8').'…';
    }
    $start = self::getCutStart($start, $length, $keywordLength, $orignalLength);
    $result = mb_substr($valueName, $start, $length, 'UTF-8');
    if ($start !== 0) {
      $result = '…'.$result;
    }
    if ($start + $length < $orignalLength) {
      $result .= '…';
    }
    return $result;
  }

  private static function getCutStart(
    $start, $length, $keywordLength, $orignalLength
  ) {
    $start = $start - intval(($length - $keywordLength) / 2);
    if ($start < 0) {
      return 0;
    }
    if ($start + $length > $orignalLength) {
      return $orignalLength - $length;
    }
    return $start;
  }
}