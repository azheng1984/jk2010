<?php
class SearchRelatedQueryScreen {
  public static function render() {
    if (!isset($GLOBALS['QUERY']['related_list'])) {
      return;
    }
    $relatedList = explode(',', $GLOBALS['QUERY']['related_list']);
    $columnAmount = 4;
    foreach ($relatedList as $query) {
      if (mb_strlen($query, 'UTF-8') > 15) {
        $columnAmount = 2;
        break;
      }
    }
    $index = 0;
    echo '<div id="related"><h2>相关搜索:</h2><table><tr>';
    foreach ($relatedList as $query) {
      if ($index % $columnAmount === 0 && $index !== 0) {
        echo '</tr><tr>';
      }
      echo '<td><a href="/', urlencode($query), '/">', $query, '</a></td>';
      ++$index;
    }
    if ($index % $columnAmount !== 0 && $index > $columnAmount) {
      $colspan = $columnAmount - $index % $columnAmount;
      $colspanAttribute = $colspan === 1 ? '' : ' colspan="'.$colspan.'"';
      echo '<td', $colspanAttribute, '></td>';
    }
    echo '</tr></table></div>';
  }
}