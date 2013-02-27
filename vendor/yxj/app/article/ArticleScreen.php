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
    $this->printBreadcrumb();
    DbConnection::connect('youxuanji');
    $authorName = Db::getColumn('SELECT name FROM user WHERE id = ?', $book['user_id']);
    DbConnection::close();
    echo '<h1>', $book['title'], '</h1>';
    echo '作者：<img src="/asset/img/avatar_middle.jpg" /> <a href="/user-', $book['user_id'], '/">', $authorName, '</a>';
    echo '<div><a href="like">喜欢</a> { ', $book['like_amount'], ' } | <a href="watch">关注</a> { 0 } | <a href="flag">举报</a></div>';
    NavigationScreen::render();
    if (isset($_SESSION['user_id']) && $book['user_id'] === $_SESSION['user_id']) {
      echo '<a href="edit">编辑</a>';
    }
    echo '<div>攻略被浏览 ', $book['page_view'], ' 次</div>';
    echo '<div id="abstract">'.$book['abstract'].'</div>';
    echo '<div id="content">'.$book['content'].'</div>';
  }

  private function printBreadcrumb() {
    $categoryList = array();
    $id = $this->book['category_id'];
    while ($id !== '0') {
      DbConnection::connect('youxuanji');
      $category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
      DbConnection::close();
      array_unshift($categoryList, $category);
      $id = $category['parent_id'];
    }
    $list = array();
    echo '<a href="/">首页</a> &gt; ';
    foreach ($categoryList as $category) {
      $list[] = '<a href ="/category-'.$category['id'].'/">'.$category['name'].'</a>';
    }
    echo implode(' &gt; ', $list);
  }
}