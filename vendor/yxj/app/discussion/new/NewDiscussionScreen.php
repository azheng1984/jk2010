<?php
class NewDiscussionScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<p>分享选择的智慧</p>';
    echo '<h1>优选集</h1>';
    NavigationScreen::render();
    echo '<form action="." method="POST">';
    echo '主题：<input name="title" />';
    echo '内容：<textarea name="content"></textarea>';
    echo '<input type="submit" value="递交" />';
    echo '</form>';
  }
}