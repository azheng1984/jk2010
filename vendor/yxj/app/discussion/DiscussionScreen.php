<?php
class DiscussionScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
    echo '<h1>', $book['title'], '</h1>';
    NavigationScreen::render();
    echo '<p><b>最近讨论</b> | 最热讨论</p>';
    echo '<div><a href="new">+ 新建主题</a></div>';
    $items = Db::getAll('SELECT * FROM topic ORDER BY last_post_time DESC');
    foreach ($items as $item) {
      echo '<img src="/asset/img/avatar_small.jpg"><p class="t_title"><a href="topic-', $item['id'], '/">',
        $item['title'], '</a></p><div id="stat"> ';
      echo '<div class="reply"><span class="big reply">', $item['post_amount'],
      '</span> <br /> 回复 </div> <div><span class="big">',
      $item['like_amount'], '</span> <br /> 赞 </div> <div><span class="big">',
      $item['watch_amount'], '</span> <br /> 关注 </div> <div><span class="big">',
      $item['page_view'], '</span> <br /> 浏览 </div></div>';
      echo '<p>', $this->printUserLink($item['user_id']), ' <span class="id"></span> ', $item['creation_time'] ,' | 最后回复: ', $this->printUserLink($item['last_post_user_id']), ' <span class="id"></span> ', $item['last_post_time'],
        '</p>';
    }
  }

  private function printUserLink($userId) {
    DbConnection::connect('youxuanji');
    $userName = Db::getColumn('SELECT name FROM user WHERE id = ?', $userId);
    DbConnection::close();
    echo '<a href="/user-', $userId, '/">', $userName, '</a>';
  }
}