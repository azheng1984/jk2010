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
    if ($category['table_prefix'] === null) {
      foreach (DbCategory::getList($parentId) as $item) {
        echo '<a href="'.urlencode($item['name']).'/">'.$item['name'].'</a> ';
      }
      return;
    }
    $this->renderProductList($category);
  }

  private function renderProductList($category) {
    echo '<ul id="product_list">';
    foreach (DbProduct::getList($category['table_prefix']) as $item) {
      echo '<li class="item"><div class="product_image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><div class="price_block">￥<span class="price">122</span> 7个商城</div><h2><a href="/'.$item['id'].'">'
        .$item['name'].'</a></h2></li>';
    }
    foreach (DbProduct::getList($category['table_prefix']) as $item) {
      echo '<li class="item"><div class="product_image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><div class="price_block">￥<span class="price">122</span> 7个商城</div><h2><a href="/'.$item['id'].'">'
        .$item['name'].'</a></h2></li>';
    }
    echo '</ul>';
  }
}