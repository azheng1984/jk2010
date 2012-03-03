<?php
class SearchExcerptionScreen {
  private static $cuttingList = array();
  private static $keywordList;

  public static function initialize($keywordList) {
    self::$keywordList = $keywordList;
  }

  public static function excerpt($text) {
    $list = array();
    $isLink = true;
    $orignalAmount = 0;
    foreach (explode("\n", $text) as $propertyText) {
      if ($propertyText === '') {
        $isLink = false;
        continue;
      }
      $list[$propertyText] = $isLink;
      ++$orignalAmount;
    }
    if (mb_strlen($text, 'UTF-8') > 60) {
      $list = self::reducePropertyList($list);
    }
    $linkList = array();
    $textList = array();
    $linkAmount = 0;
    $textAmount = 0;
    foreach ($list as $propertyText => $isLink) {
      if (isset($GLOBALS['CATEGORY']) === false || $isLink === false) {
        $textList[] = $propertyText;
        ++$textAmount;
        continue;
      }
      if (isset($GLOBALS['PROPERTY_LIST'])) {
        $propertyText = self::removeCurrentProperty($propertyText);
      }
      if ($propertyText !== null) {
        $linkList[] = $propertyText;
        ++$linkAmount;
      }
    }
    $amount = $linkAmount + $textAmount;
    if ($amount === 0) {
      return;
    }
    $isFull = $amount === $orignalAmount;
    $count = 0;
    $result = '';
    if ($linkAmount !== 0) {
      $result .= '<ul class="link_list">';
      foreach ($linkList as $item) {
        ++$count;
        $end = isset(self::$cuttingList[$item])
          || ($count === $amount && $isFull === false) ? '…' : '';
        $result .= '<li>'.$item.$end.'</li>';
      }
      $result .= '</ul>';
    }
    if ($textAmount !== 0) {
      $result .= '<ul>';
      foreach ($textList as $item) {
        ++$count;
        $end = isset(self::$cuttingList[$item])
          || ($count === $amount && $isFull === false) ? '…' : '';
        $result .= '<li>'.$item.$end.'</li>';
      }
      $result .= '</ul>';
    }
    if ($isFull === false) {
      echo $result .= '<p>…</p>';
    }
    return $result;
  }

  private static function removeCurrentProperty($text) {
    $list = explode('：', $text, 2);
    if (count($list) !== 2) {
      return;
    }
    list($keyName, $valueName) = $list;
    if (isset($GLOBALS['PROPERTY_LIST'][$keyName]) === false) {
      return $text;
    }
    if (strpos($valueName, '；') === false) {
      return isset(
          $GLOBALS['PROPERTY_LIST'][$keyName]['value_list'][$valueName]
      ) ? null : $text;
    }
    $valueNameList = array();
    foreach (explode('；', $valueName) as $item) {
      if (isset($GLOBALS['PROPERTY_LIST'][$keyName]['value_list'][$item])) {
        continue;
      }
      $valueNameList[] = $item;
    }
    if (count($valueNameList) === 0) {
      return;
    }
    return $keyName.'：'.implode('；', $valueNameList);
  }

  private static function reducePropertyList($list) {
    $length = 0;
    $result = array();
    $matchList = array();
    foreach (self::$keywordList as $keyword) {
      foreach ($list as $propertyText => $isLink) {
        if (strpos($propertyText, $keyword) === false) {
          continue;
        }
        if (isset($result[$propertyText]) === false) {
          $result[$propertyText] = $isLink;
          $propertyLength = mb_strlen($propertyText, 'UTF-8');
          $matchList[] = array($propertyText, $isLink, $propertyLength);
          $length += $propertyLength;
        }
        break;
      }
    }
    if ($length < 60) {
      return self::increaseExcerption($list, $result, $length);
    }
    for ($index = count($matchList) - 1; $index > -1; --$index) {
      $item = $matchList[$index];
      $propertyText = $item[0];
      $isLink = $item[1];
      $propertyLength = $item[2];
      if ($propertyLength > 12) {
        $cut = self::cutProperty($propertyText, $isLink, $propertyLength);
      }
      if ($cut !== null) {
        $matchList[$index] = array($cut[0], $isLink, $cut[1]);
        $length -= $propertyLength - $cut[1];
        self::$cuttingList[$cut[0]] = true;
      }
      if ($length < 60) {
        break;
      }
    }
    $length = 0;
    $result = array();
    foreach ($matchList as $item) {
      if ($length + $item[2] > 60 && $length > 0) {
        break;
      }
      $result[$item[0]] = $item[1];
      $length += $item[2];
    }
    return $result;
  }

  private static function cutProperty($text, $isLink, $length) {
    if ($isLink === false) {
      return array(mb_substr($text, 0, 12, 'UTF-8'), 12);
    }
    $endPosition = mb_strpos($text, '；', 0, 'UTF-8');
    if ($endPosition === false) {
      return;
    }
    for (;;) {
      $position = mb_strpos($text, '；', $endPosition, 'UTF-8');
      if ($position === false || $position > 12) {
        break;
      }
      $endPosition = $position;
    }
    return array(mb_substr($text, 0, $endPosition, 'UTF-8'), $endPosition);
  }

  private static function increaseExcerption($list, $result, $length) {
    if (count($list) === count($result)) {
      return $result;
    }
    foreach ($list as $propertyText => $isLink) {
      if (isset($result[$propertyText])) {
        continue;
      }
      $propertyLength = mb_strlen($propertyText, 'UTF-8');
      $length += $propertyLength;
      if ($length > 60) {
        break;
      }
      $result[$propertyText] = $isLink;
    }
    return $result;
  }
}