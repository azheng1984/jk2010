<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    $this->renderDescription();
    $this->renderCategoryList();
    echo '</div>';
  }

  private function renderDescription() {
    $amount = require DATA_PATH.'user_and_article_amount.php';
    echo '<div id="description">',
      '<h1>分享经验，集思广益。</h1>',
      '<div>这里已经聚集了 ', $amount[0], ' 位用户，', $amount[1], ' 篇攻略。</div>',
      '<a href="/about">了解更多</a>',
    '</div>';
  }

  private function renderCategoryList() {
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = 0 AND is_active = 1',
        ' ORDER BY popularity_rank DESC'
    );
    echo '<ul id="category_list">';
    foreach ($categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '">',
        $category['name'], '</a></li>';
    }
    echo '</ul>';
  }
}