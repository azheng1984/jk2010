<?php
class RelatedQueryScreen {
  public static function render() {
    $query = DbQuery::getByName($GLOBALS['URI']['QUERY']);
    if ($query === false || $query['related_list'] === null) {
      return;
    }
    $buffer = array();
    foreach (explode(',', $query['related_list']) as $item) {
      $buffer[] = '<td><a href="/'.urlencode($item).'/">'
        .$item.'</a></td>';
    }
    if (count($buffer) === 0) {
      return;
    }
    //TODO: 用 table 代替
    echo '<div id="related"><h2>相关搜索:</h2><table><tr>', implode('', $buffer), '</tr><tr>', implode('', array_reverse($buffer)), '</tr></table></div>';
  }
}