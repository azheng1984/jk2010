<?php
namespace YouXuanJi\App\Brand;

class BrandScreen {
  private $relatedCategoryList = array();

  public function render() {
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $brand = Db::getRow('SELECT * FROM brand WHERE id = ?', $id);
    Db::execute('UPDATE brand SET page_view = page_view + 1 WHERE id = ?', $id);
    echo '<a href="edit">编辑</a>';
    if ($brand['is_active'] === '0') {
      echo '<div>条目已经过期</div>';
    }
    echo '<h1>', $brand['name'], '</h1>';
    if ($brand['logo_folder_id'] !== null) {
      echo '<div>logo</div>';
    }
    echo '<div>', $brand['abstract'], '</div>';
    echo '<div>浏览：', $brand['page_view'], '</div>';
    $location = Db::getRow(
      'SELECT * FROM location WHERE id = ?', $brand['location_id']
    );
    if ($location !== false) {
      echo '<div>品牌发源地：<a href="/location-', $brand['location_id'], '/">', $location['name'], '</a></div>';
    }
    echo '<div>', $brand['content'], '</div>';
    echo '<div><a href="flag">纠错</a></div>';
    $brand['content'];
    $relatedBrandList = array();
    
    if ($brand['parent_id'] !== null) {
      $relatedBrandList[] = Db::getRow('SELECT * FROM brand WHERE id = ?', $brand['parent_id']);
      $tmp = Db::getAll('SELECT * FROM brand WHERE parent_id = ? LIMIT 9', $brand['parent_id']);
      if (count($tmp) !== 0) {
        $relatedBrandList = array_merge($tmp, $relatedBrandList);
      }
    }
    if (count($relatedBrandList) !== 0) {
      echo '<h2>立顿相关的品牌</h2>';
      echo '<ul>';
      foreach($relatedBrandList as $relatedBrand) {
        echo '<li>', $relatedBrand['name'], '</li>';
      }
      echo '</ul>';
    }
    echo '<h2>立顿相关的品牌分类</h2>';
    $categoryList = Db::getAll('SELECT category_id FROM brand_category WHERE brand_id = ?', $brand['id']);
    foreach ($categoryList as $category) {
      $this->printBreadcrumb($category['category_id']);
    }
    echo '<h2>立顿相关的十大品牌排名投票</h2>';
    $this->printTopList();
  }

  private function printBreadcrumb($id) {
    $categoryList = array();
    while ($id !== '0' && $id !== null) {
      $category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
      array_unshift($categoryList, $category);
      $id = $category['parent_id'];
    }
    $list = array();
    foreach ($categoryList as $category) {
      $list[] = '<a href ="/category-'
        .$category['id'].'/">'.$category['name'].'</a>';
      $this->relatedCategoryList[$category['id']] = $category['name'];
    }
    echo implode(' › ', $list);
  }

  private function printTopList() {
    foreach ($this->relatedCategoryList as $id => $name) {
      echo '<a href ="/category-'
        .$id.'/top/">十大'.$name.'品牌排名</a>';
    }
  }
}
