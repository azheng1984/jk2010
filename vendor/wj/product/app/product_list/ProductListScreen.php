<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    $breadcrumb = new Breadcrumb;
    $breadcrumb->render();
    echo ' <h1>'.$GLOBALS['category']->getName().'</h1>';
    $table = $GLOBALS['category']->getTablePrefix().'_property_active_key a left join '.$GLOBALS['category']->getTablePrefix()
    .'_property_key b on a.key_id=b.id';
    $sql = "select b.* from $table";
    $statement = Db::get($sql);
    $statement->execute();
    $filters = array();
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $sql = "select a.* from wj.laptop_property_value a left join wj.laptop_property_active_value b on a.id=b.value_id where a.key_id=".$row['id'];
      $statement2 = Db::get($sql);
      $statement2->execute();
      echo '<div>'.$row['key'].'</div>';
      $values = $statement2->fetchAll(PDO::FETCH_ASSOC);
      array_unshift($values, array('value' => '全部'));
      foreach ($values as $row2) {
        echo '<a ';
        if (isset($_GET[$row['key']]) && $_GET[$row['key']] === $row2['value']) {
          echo 'style="background:#000;color:#fff;"';
          if ($row2['value'] !== '全部') {
            $filters[] = $row2['id'];
          }
        }
        echo ' href="?'.$row['key'].'='.$row2['value'].'">'.$row2['value'].'</a> ';
      }
    }
    if (count($filters) !== 0) {
      $this->search($filters);
    } else {
      foreach (Product::getList() as $product) {
        echo '<div><a href="'.$product['path'].'">'.$product['name'].'</a></div>';
      }
    }
  }

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