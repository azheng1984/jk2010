<?php
class HomeScreen extends Screen {
  private $categoryList;

  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 品牌消费导航</title>';
    $this->categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = 0 AND is_active = 1',
      ' ORDER BY popularity_rank DESC'
    );
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    $this->renderDescription();
    $this->renderBrandList();
    $this->renderCategoryList();
    $this->renderLocationList();
    echo '</div>';
  }

  private function renderDescription() {
    echo '<div>slogan</div>';
  }

  private function renderBrandList() {
    echo '<ul id="category_list">';
    foreach ($this->categoryList as $category) {
      $brand = Db::getRow(
        'SELECT b.* FROM brand_category bc LEFT JOIN brand b'
          .' ON bc.brand_id = b.id WHERE category_id = ?'
          .' ORDER BY popularity_rank LIMIT 1',
        $category['id']);
      echo '<li><a href="/brand-', $brand['id'], '/">',
        $brand['name'], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderCategoryList() {
    echo '<h2>品牌分类</h2>';
    echo '<ul id="category_list">';
    foreach ($this->categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/">',
        $category['name'], '</a></li>';
    }
    echo '</ul>';
  }

  private function renderLocationList() {
    echo '<h2>品牌发源地</h2>';
    $locationList = Db::getAll(
      'SELECT * FROM location WHERE parent_id = 0 AND is_active = 1',
        ' ORDER BY pinyin DESC'
    );
    echo '<ul id="category_list">';
    foreach ($locationList as $location) {
      echo '<li><a href="/location-', $location['id'], '/">',
      $location['name'], '</a></li>';
    }
    echo '</ul>';
  }
}