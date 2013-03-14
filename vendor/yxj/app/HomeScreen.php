<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 品牌聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    $this->renderDescription();
    $this->renderCategoryList();
    echo '</div>';
  }

  private function renderDescription() {
  }

  private function renderCategoryList() {
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = 0 AND is_active = 1',
        ' ORDER BY popularity_rank DESC'
    );
    echo '<ul id="category_list">';
    foreach ($categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/">',
        $category['name'], '</a></li>';
    }
    echo '</ul>';
  }
}