<?php
class RelatedQueryScreen {
  public static function render() {
    $query = DbQuery::getByName($GLOBALS['URI']['QUERY']);
    if ($query === false || $query['related_list'] === null) {
      return;
    }
    $buffer = '';
    foreach (explode(',', $query['related_list']) as $item) {
      $buffer .= '<li><a href="/'.urlencode($item).'/">'
        .$item.'</a></li>';
    }
    if ($buffer === '') {
      return;
    }
    //TODO: 用 table 代替
    echo '<div id="related"><h2>相关搜索:</h2><ul>', $buffer, '</ul></div>';
  }
}