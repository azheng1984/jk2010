<?php
class DiscussionUserScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM article WHERE id = 1');
    echo '<h1>', $book['name'], '</h1>';
    NavigationScreen::render();
    echo '<p>广告</p>';
  }
}