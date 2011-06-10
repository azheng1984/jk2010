<?php
class ProductListScreen extends Screen {
  public function renderContent() {
    echo '[<a href="/category/edit?id='.$_GET['category_id'].'">编辑</a> | <a href="/product/new?category_id='.$_GET['category_id'].'">新建产品</a>]';
    $category = Category::getById($_GET['category_id']);
    echo ' <h1>'.$category->getName().'</h1>';
    $table = $category->getTablePrefix().'_property_active_key a left join '.$category->getTablePrefix()
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
        echo ' href="?category_id=3&'.$row['key'].'='.$row2['value'].'">'.$row2['value'].'</a> ';
      }
    }
    if (count($filters) !== 0) {
      $this->search($filters, $category);
    } else {
      foreach (Product::getList($category) as $product) {
        echo '<div><a href="product?category_id='.$category->getId().'&id='.$product['id'].'">'.$product['name'].'</a></div>';
      }
    }
  }

  private function search($filters, $category) {
    require_once 'sphinxapi.php';;
    $s = new SphinxClient;
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(3);
    $s->SetSortMode(SPH_SORT_ATTR_DESC, 'rank');
    $result = $s->query(implode(' ', $filters));
    foreach ($result['matches'] as $id => $values) {
      $product = Product::getById($category, $id);
      echo '<div><a href="product?category_id='.$category->getId().'&id='.$product['id'].'">'.$product['name'].'</a></div>';
    }
  }
}