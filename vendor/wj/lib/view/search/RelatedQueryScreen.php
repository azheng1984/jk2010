<?php
class RelatedQueryScreen {
  public static function render() {
    $result = QuerySearch::search(0.6);
    if ($result['total_found'] === 0) {
      return;
    }
    $buffer = '';
    foreach ($result['matches'] as $id => $item) {
      $query = DbQuery::get($id);
      if ($GLOBALS['URI']['QUERY'] === $query['name']) {
       continue;
      }
      $buffer .= '<li><a href="/'.$query['name'].'/">'
        .$query['name'].'</a> '.$item['attrs']['product_amount'].'</li>';
    }
    if ($buffer === '') {
      return;
    }
    echo '<h2>相关搜索:</h2><ul>', $buffer, '</ul>';
  }
}