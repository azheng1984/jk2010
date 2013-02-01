<?php
class CategoryScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="category" class="content">';
    echo '<h1>其他</h1>';
    echo '<div id="sort">排序：<strong>热门</strong> | <a href="?sort=time">创建时间</a></div>';
    $list = Db::getAll('SELECT * FROM article WHERE category_id = 1');
    echo '<ol>';
    foreach ($list as $item) {
      echo '<li>';
      echo '<a href="/article-', $item['id'], '/">', $item['name'], '</a>';
      echo '<div>', $item['abstract'], '</div>';
      echo '</li>';
    }
    echo '</ol>';
    echo '</div>';
  }
}