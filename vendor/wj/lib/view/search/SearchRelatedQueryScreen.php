<?php
class SearchRelatedQueryScreen {
  public static function render() {
    $query = DbQuery::getByName($GLOBALS['QUERY']);
    if ($query === false || $query['related_list'] === null) {
      return;
    }
    $hasLongText = false;
    $relatedList = explode(',', $query['related_list']);
    foreach ($relatedList as $query) {
      if (mb_strlen($query, 'UTF-8') > 30) {
        $hasLongText = true;
        break;
      }
    }
    $maxColumn = 4;
    $maxRow = 4;
    if ($hasLongText) {
      $maxColumn = 2;
      $maxRow = 8;
    }
    $amount = count($relatedList);
    $index = 0;
    echo '<table>';
    for ($row = 0; $row < $maxRow; ++$row) {
      echo '<tr>';
      for ($column = 0; $column < $maxRow; ++$column, ++$index) {
        if ($index < $amount) {
          $query = $relatedList[$index];
          echo '<td><a href="/', urlencode($query), '/">', $query, '</a></td>';
          continue;
        }
        echo '<td class="empty"></td>';
      }
      echo '</tr>';
      if ($index >= $amount) {
        break;
      }
    }
    echo '</table>';
  }
}