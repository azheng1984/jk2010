<?php
class HomeScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="home" class="content">';
    echo '<div id="description">Slogan：Who am I <a href="/about/">了解更多</a></div>';
    DbConnection::connect('youxuanji');
    $categoryList = Db::getAll('SELECT * FROM category WHERE parent_id = 0');
    DbConnection::close();
    echo '<div id="category_list">';
    foreach ($categoryList as $category) {
      echo '<p><a href="/category-', $category['id'], '/">', $category['name'], '</a></p>';
    }
    echo '</div>';
    echo '</div>';
  }
}