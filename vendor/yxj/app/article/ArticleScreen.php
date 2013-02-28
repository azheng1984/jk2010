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
    $author = Db::getRow('SELECT * FROM user WHERE id = ?', $book['user_id']);
    DbConnection::close();
    echo '<h1>', $book['title'], '</h1>';
    NavigationScreen::render();
    echo '<p><a href="like">喜欢</a> { ', $book['like_amount'], ' } | <a href="watch">关注</a> { 0 } | 浏览 {', $book['page_view'], '} | <a href="flag">举报</a></p>';
    echo '攻略作者<p><img src="/asset/img/avatar_middle.jpg" /></p> <p><a href="/user-', $book['user_id'], '/">', $author['name'], '</a></p>';
    echo '<p>', $author['signature'], '</p>';
    echo '<p>帐号: ',$author['id'], '</p>';
    echo '<p>声望: ',$author['reputation'], '</p>';
    echo '<p>攻略: ',$author['article_amount'], '</p>';
    echo '{绑定帐号}';
    if (isset($_SESSION['user_id']) && $book['user_id'] === $_SESSION['user_id']) {
      echo '<a href="edit">编辑</a>';
    }
    echo '<p id="menu">目录</p>';
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
    echo '<a href="/">首页</a> › ';
    foreach ($categoryList as $category) {
      $list[] = '<a href ="/category-'.$category['id'].'/">'.$category['name'].'</a>';
    }
    echo implode(' › ', $list);
  }
}