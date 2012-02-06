<?php
class HomeScreen {
  public function __construct() {
    header('Cache-Control: public, max-age=3600');
    header('Expires: '
        .gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + 3600).' GMT');
  }

  public function render() {
    echo 'suggest({';
    $result = QuerySearch::search();
    if (isset($result['matches'])) {
      foreach ($result['matches'] as $id => $item) {
        $query = DbQuery::get($id);
        echo '"'.$query['name'], '":', $item['attrs']['product_amount'].',';
      }
    }
    echo '});';
  }
}