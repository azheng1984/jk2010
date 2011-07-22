<?php
class ProductSearch {
  private function search($filters) {
    require_once 'sphinxapi.php';;
    $s = new SphinxClient;
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(3);
    $s->SetSortMode(SPH_SORT_ATTR_DESC, 'rank');
    $result = $s->query(implode(' ', $filters));
    foreach ($result['matches'] as $id => $values) {
      $product = Product::getById($id);
      echo '<div><a href="'.$product['path'].'">'.$product['name'].'</a></div>';
    }
  }
}