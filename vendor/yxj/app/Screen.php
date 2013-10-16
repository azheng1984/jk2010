<?php
namespace Yxj\App;

class Screen extends \Yxj\View\Screen {
  private $categoryList;

  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 品牌消费社区</title>';
    $this->categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = 0 AND is_active = 1'
        .' ORDER BY popularity_rank DESC'
    );
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    $this->renderDescription();
    $this->renderBrandList();
    $this->renderCategoryList();
    $this->renderLocationList();
    $this->renderTopList();
    echo '</div>';
  }

  private function renderDescription() {
    echo '<div></div>';
  }

  private function renderBrandList() {
    echo '<a href="/brand/new">添加品牌</a>';
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
    echo '<div>品牌分类</div>';
    echo '<div><a href="/category/new">添加</a></div>';
    echo '<ul id="category_list">';
    foreach ($this->categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/">',
        $category['name'], '</a>';
      if ($category['brand_amount'] === '0') {
        echo ' 删除';
      }
      echo '</li>';
    }
    echo '</ul>';
  }

  private function renderLocationList() {
    echo '<div>品牌发源地</div>';
    echo '<div><a href="/location/new">添加</a></div>';
    $locationList = Db::getAll(
      'SELECT * FROM location WHERE parent_id = 0 AND is_active = 1',
        ' ORDER BY pinyin DESC'
    );
    echo '<ul id="category_list">';
    foreach ($locationList as $location) {
      echo '<li><a href="/location-', $location['id'], '/">',
      $location['name'], '</a>';
      if ($location['brand_amount'] === '0') {
        echo ' 删除';
      }
      echo '</li>';
    }
    echo '</ul>';
  }

  private function renderTopList() {
    echo '<div>十大品牌排名投票</div>';
    echo '<ul id="category_list">';
    foreach ($this->categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/top/">十大',
      $category['name'], '品牌排名</a></li>';
    }
    echo '</ul>';
  }
}
