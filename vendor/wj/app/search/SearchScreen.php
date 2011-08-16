<?php
class SearchScreen extends Screen {
  public function renderContent() {
//    require 'sphinxapi.php';
//    $s = new SphinxClient;
//    $s->setServer("localhost", 9312);
//    $s->setMatchMode(SPH_MATCH_ANY);
//    $s->setMaxQueryTime(3);
//    $result = $s->query("1");
//    if (isset($result['matches'])) {
//      foreach ($result['matches'] as $id => $value) {
//        print_r(DbProduct::get('laptop', $id));
//      }
//    }
    $categories = array();
    $category = null;
    $parentId = 0;
    foreach ($_GET['categories'] as $categoryName) {
      if ($categoryName !== '') {
        $category = DbCategory::getByName(urldecode($categoryName), $parentId);
        if ($category === false) {
          throw new NotFoundException;
        }
        $categories[] = $category;
        $parentId = $category['id'];
        if ($category['table_prefix'] !== null) {
          break;
        }
      }
    }
    $breadcrumb = new Breadcrumb($categories);
    $breadcrumb->render();
    echo '<div id="category_title"><h1>'.$category['name'].'</h1></div>';
    if ($category['table_prefix'] === null) {
      echo '<ul id="category_list">';
      foreach (DbCategory::getList($parentId) as $category) {
        echo '<li><a href="'.urlencode($category['name']).'/">'
          .$category['name'].'</a></li>';
      }
      echo '</ul>';
      return;
    }
    $this->renderProductList($category);
  }

  private function renderProductList($category) {
    foreach (DbProperty::getList($category['id']) as $item) {
      echo '<div>'.$item['key'].':';
      foreach ($item['values'] as $value) {
        echo ' <a href="?'.urlencode($item['key']).'='.urlencode($value['value']).'">'.$value['value'].'</a>';
      }
      echo '</div>';
    }
    echo '<ul id="product_list">';
    for ($index = 0; $index < 5; $index++) {
      foreach (DbProduct::getList($category['table_prefix']) as $item) {
        echo '<li class="item"><div class="product_image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a href="/'.$item['id'].'">'
          .$item['name'].'</a></h2><div class="price_block">￥<span class="price">10000.00</span>~<span class="price">12299.00</span> <div>7个商城</div></div></li>';
      }
    }
    echo '</ul>';
  }
}