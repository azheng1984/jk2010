<?php
class HomeScreen {
  public function __construct() {
    header('Cache-Control: public,max-age=3600');
    header('Expires: '
        .gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + 3600).' GMT');
    header('Content-Type: application/json;charset=utf-8');
  }

  public function render() {
    $queryName = urldecode(substr($_SERVER['REQUEST_URI'], '1'));
    $result = false;
    if ($queryName !== '') {
      $queryName = SegmentationService::execute($queryName);
      if ($queryName !== '') {
        $result = QuerySearch::search($queryName);
      }
    }
    $list = array();
    if ($result !== false && isset($result['matches'])) {
      foreach ($result['matches'] as $id => $item) {
        $query = DbQuery::get($id);
        $list[] = '"'.$query['name'].'":'.$item['attrs']['product_amount'];
      }
    }
    if (count($list) === 0) {
      echo 'huobiwanjia.suggestion.execute(null, null);';
      return;
    }
    echo 'huobiwanjia.suggestion.execute("', $queryName,
      '",{' ,implode(',', $list), '});';
  }
}