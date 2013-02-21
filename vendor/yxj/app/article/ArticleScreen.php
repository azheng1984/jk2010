<?php
class ArticleScreen extends Screen {
  private $book;

  public function __construct() {
    $this->book = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
  }

  protected function renderHtmlHeadContent() {
    echo '<title>', $this->book['name'], ' - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = $this->book;
    DbConnection::connect('youxuanji');
    $authorName = Db::getColumn('SELECT name FROM user WHERE id = ?', $book['user_id']);
    DbConnection::close();
    echo '<h1>', $book['title'], '</h1>';
    echo '作者：<a href="/user-', $book['user_id'], '/">', $authorName, '</a>';
    echo '<div>喜欢 < ', $book['like_amount'], ' > | 关注 | 举报</div>';
    NavigationScreen::render();
    echo '<a href="edit">编辑</a>';
    echo '<div class="abstract">'.$book['abstract'].'</div>';
    echo '<div class="content">'.$book['content'].'</div>';
    echo '<p>版权：</p>';
    echo '<p>广告</p>';
  }
}