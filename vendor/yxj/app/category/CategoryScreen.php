<?php
class CategoryScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h1>分类1</h1>';
    echo '<div>排序：<strong>热门</strong> | <a href="?sort=time">创建时间</a></div>';
    $list = Db::getAll('SELECT * FROM article_category LEFT JOIN article ON article_category.article_id = article.id WHERE category_id = 1');
    foreach ($list as $item) {
      print_r($item);
    }
  }
}