<?php
class TopicScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  private $topic, $article;

  protected function renderHtmlBodyContent() {
    $article = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    $this->article = $article;
    $tmp = explode('-', $GLOBALS['PATH_SECTION_LIST'][3]);
    $topic = Db::getRow('SELECT * FROM topic WHERE id = ?', $tmp[1]);
    $this->topic= $topic;
    echo '<p>';
    NavigationScreen::render();
    echo '</p>';
    echo '<h1>', $topic['title'], '</h1>';
    echo '<p>关注 {',$topic['watch_amount'],'} | 浏览　{',$topic['page_view'],'}</p>';
    echo '<table><tr><td>';
    $this->printUserInfo($topic['user_id']);
    echo '</td><td>';
    echo $topic['content'];
    $this->printToolbar($topic);
    echo '</td></tr>';
    $items = Db::getAll('SELECT * FROM post WHERE topic_id = ? ORDER BY id', $GLOBALS['PATH_SECTION_LIST'][4]);
    foreach ($items as $item) {
      echo '<tr><td>';
      $this->printUserInfo($item['user_id']);
      echo '</td><td>', $item['content'];
      $this->printToolbar($item);
      echo '</td></tr>';
    }
    echo '</table>';
    echo '<div><a href="new">+ 回应</a></div>';
  }

  private function printUserInfo($userId) {
    DbConnection::connect('youxuanji');
    $user = Db::getRow('SELECT * FROM user WHERE id = ?', $userId);
    DbConnection::close();
    echo '<p><img src="/asset/img/avatar_middle.jpg" /></p>';
    echo '<p><a href="/user-', $userId, '/">', $user['name'], '</a></p>';
    echo '<p>', $user['signature'], '</p>';
    echo '<p>帐号：',$user['id'], '</p>';
    echo '<p>声望：',$user['reputation'], '</p>';
    echo '<p>攻略：',$user['article_amount'], '</p>';
    echo '{绑定帐号}';
    if ($userId === $this->topic['user_id']) {
      echo '[楼主]';
    }
    if ($userId === $this->article['user_id']) {
      echo '[攻略作者]';
    }
  }

  private function printToolbar($item) {
    echo '<p>';
    echo $item['creation_time'];
    if (isset($_SESSION['user_id']) && $item['user_id'] === $_SESSION['user_id']) {
      echo ' | 修改 | 删除';
    }
    echo ' | 分享 | <a href="#">赞</a> {0} | 举报';
    echo '</p>';
  }
}