<?php
class Router {
  public function execute() {
    if ($_SERVER['HTTP_HOST'] === 'www.huobiwanjia.com') {
      header('Location: http://huobiwanjia.com'.$_SERVER['REQUEST_URI']);
      return '/redirect';
    }
    if ($_SERVER['HTTP_HOST'] === 'img.workbench.wj.com') {
      return '/image';
    }
    if ($_SERVER['HTTP_HOST'] !== 'huobiwanjia.com'
      && $_SERVER['HTTP_HOST'] !== 'workbench.wj.com') {
      throw new NotFoundException;
    }
    if ($_SERVER['REQUEST_URI'] === '/') {
      return '/';
    }
    $tmps = explode('?', $_SERVER['REQUEST_URI'], 2);
    $sections = explode('/', $tmps[0]);
    if (count($sections) === 2 && $sections[1] !== '') {
      $_GET['product_id'] = $sections[1];
      return '/product';
    }
    $this->bindingCategories($sections);
    if ($this->isLeafCategory(end($_GET['categories']))) {
      return '/product_list';
    }
    return '/category_list';
  }

  private function bindingCategories($sections) {
    $categories = array();
    $parentId = 0;
    foreach ($sections as $categoryName) {
      if ($categoryName === '') {
        continue;
      }
      $category = DbCategory::getByName(urldecode($categoryName), $parentId);
      if ($category === false) {
        throw new NotFoundException;
      }
      $categories[] = $category;
      $parentId = $category['id'];
      if ($this->isLeafCategory($category)) {
        break;
      }
    }
    $_GET['categories'] = $categories;
  }

  private function isLeafCategory($category) {
    return $category['table_prefix'] !== null;
  }
}