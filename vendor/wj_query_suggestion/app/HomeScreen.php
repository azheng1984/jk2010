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
      echo 'huobiwanjia.screen.suggest(null, null);';
      return;
    }
    //TODO:如果只有一条，而且是 queryname 本身，那提示就没有意义
    echo 'huobiwanjia.screen.suggest("', $queryName,
      '",{' ,implode(',', $list), '});';
  }
}