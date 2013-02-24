<?php
class NewArticleScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
    echo '<script src="/asset/js/markdown.js"></script>';

  }

  protected function renderHtmlBodyContent() {
//     $book = Db::getRow('SELECT * FROM book WHERE id = 1');
//     echo '<h1>', $book['name'], '</h1>';
    //NavigationScreen::render();
    echo '<h2>写攻略</h2>';
    echo '目录预览：';
    echo '<form method="POST" action="">';
    echo '标题：<input name="title" type="text"/>';
    echo '摘要：<textarea name="abstract"></textarea>';
    echo '内容：<textarea name="content"></textarea>';
    //echo '内容预览：';
    echo '<input name="submit" type="submit" value="发布"/>';
    echo ' <input name="submit" type="submit" value="保存草稿"/>';
  }
}