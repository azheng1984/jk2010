<?php
class DiscussionScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    echo '<h1>', $book['title'], '</h1>';
    NavigationScreen::render();
    echo '<div><a href="new">+ 新建主题</a></div>';
    $items = Db::getAll('SELECT * FROM topic ORDER BY last_post_time DESC');
    foreach ($items as $item) {
      echo '<div><a href="topic-', $item['id'], '/">',
        $item['title'], '</a> ', $item['creation_time'] ,'</div>';
    }
  }
}