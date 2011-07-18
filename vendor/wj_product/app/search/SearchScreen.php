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
    echo '<ul>';
    foreach (DbProduct::getList($category['table_prefix']) as $item) {
      echo '<li class="item"><a href="/'.$item['id'].'">'
        .$item['name'].'</a></li>';
    }
    echo '</ul>';
  }
}