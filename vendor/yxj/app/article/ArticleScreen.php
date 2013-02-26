<?php
class ArticleScreen extends Screen {
  private $book;

  public function __construct() {
    $this->book = Db::getRow('SELECT * FROM article WHERE id = ?', $GLOBALS['ARTICLE_ID']);
  }

  protected function renderHtmlHeadContent() {
    echo '<title>', $this->book['title'], ' - 优选集</title>';
    $this->addCssLink('article');
  }

  protected function renderHtmlBodyContent() {
    $book = $this->book;
    DbConnection::connect('youxuanji');
    $authorName = Db::getColumn('SELECT name FROM user WHERE id = ?', $book['user_id']);
    DbConnection::close();
    echo '<h1>', $book['title'], '</h1>';
    echo '作者：<a href="/user-', $book['user_id'], '/">', $authorName, '</a>';
    echo '<div><a href="like">喜欢</a> { ', $book['like_amount'], ' } | <a href="watch">关注</a> { 0 } | <a href="flag">举报</a></div>';
    NavigationScreen::render();
    if (isset($_SESSION['user_id']) && $book['user_id'] === $_SESSION['user_id']) {
      echo '<a href="edit">编辑</a>';
    }
    echo '<div class="abstract">'.$book['abstract'].'</div>';
    echo '<div class="content">'.$book['content'].'</div>';
  }
}