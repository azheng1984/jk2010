<?php
class SearchScreen extends Screen {
  public function renderContent() {
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
    $breadcrumb = new Breadcrumb();
    $breadcrumb->render($categories);
    echo '<div id="category_title"><h1>'.$category['name'].'</h1></div>';
    if ($category['table_prefix'] === null) {
      echo '<div id="category_list">';
      foreach (DbCategory::getList($parentId) as $item) {
        echo '<a href="'.urlencode($item['name']).'/">'.$item['name'].'</a> ';
      }
      echo '</div>';
      return;
    }
    $this->renderProductList($category);
  }

  private function renderProductList($category) {
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