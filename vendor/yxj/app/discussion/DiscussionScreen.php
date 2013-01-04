<?php
class DiscussionScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h1>优选集</h1>';
    NavigationScreen::render();
    echo '<div><a href="/book/youxuanji/discussion/new">+ 新建主题</a></div>';
    $items = Db::getAll('SELECT * FROM topic ORDER BY last_post_time DESC');
    foreach ($items as $item) {
      echo '<div><a href="/book/youxuanji/discussion/', $item['id'], '/">',
        $item['title'], '</a> ', $item['creation_time'] ,'</div>';
    }
  }
}