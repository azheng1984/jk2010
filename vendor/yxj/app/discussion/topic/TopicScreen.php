<?php
class TopicScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<p>分享选择的智慧</p>';
    echo '<h1>优选集</h1>';
    NavigationScreen::render();
    echo '<div><a href="new">+ 回应</a></div>';
    $items = Db::getAll('SELECT * FROM post WHERE topic_id = ? ORDER BY id', $GLOBALS['PATH_SECTION_LIST'][4]);
    $index = 1;
    foreach ($items as $item) {
      echo '<p>', $item['content'], ' ', $item['creation_time'], ' #', $index++, '</p>';
    }
  }
}