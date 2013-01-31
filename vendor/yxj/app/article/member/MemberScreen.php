<?php
class MemberScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM book WHERE id = 1');
    echo '<h1>', $book['name'], '</h1>';
    NavigationScreen::render();
    $page = Db::getRow('SELECT * FROM page WHERE id = 1');
    echo '<h2>', Db::getColumn('SELECT content FROM line WHERE id = '.$page['name_line_id']), '</h2>';
    echo '<div><a href="/edit/1/page/1">编辑</a></div>';
    $lineList = Db::getAll('SELECT * FROM line WHERE id IN ('.$page['line_id_list'].')');
    foreach ($lineList as $line) {
      echo '<p>'.$line['content'].'</p>';
    }
    echo '<a href="..">首页</a>';
    echo '<p>广告</p>';
  }
}