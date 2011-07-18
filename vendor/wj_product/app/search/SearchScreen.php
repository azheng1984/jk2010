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
    $breadcrumb->render();
    if ($category['table_prefix'] === null) {
      foreach (DbCategory::getList($parentId) as $item) {
        echo '<a href="'.urlencode($item['name']).'/">'.$item['name'].'</a> ';
      }
    }
  }
}