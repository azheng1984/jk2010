<?php
class NewArticleScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
    echo '<script src="/asset/js/markdown.js"></script>';
  }

  protected function renderHtmlBodyContent() {
//     $book = Db::getRow('SELECT * FROM book WHERE id = 1');
//     echo '<h1>', $book['name'], '</h1>';
    NavigationScreen::render();
    echo '<h2>新建页面</h2>';
    echo '目录预览：';
    echo '<form method="POST" action="">';
    echo '序号：<input type="text"/>';
    echo '标题：<input type="text"/>';
    echo '内容：<textarea></textarea>';
    echo '内容预览：';
    echo '<input type="submit" value="确定"/>';
    echo '<input type="submit" value="取消"/>';
  }
}