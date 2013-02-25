<?php
class MentionScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集 - 攻略聚集地</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<h1>提到我的讨论</h1>';
  }
}