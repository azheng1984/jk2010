<?php
class ArticleScreen extends Screen {
  private $book;

  public function __construct() {
    $this->book = Db::getRow('SELECT * FROM article WHERE id = 1');
  }

  protected function renderHtmlHeadContent() {
    echo '<title>', $this->book['name'], ' - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    $book = $this->book;
    echo '<h1>', $book['name'], '</h1>';
    echo '作者：<a href="/user-1/">优选集</a>';
    echo '<div>喜欢 | 关注 | 举报</div>';
    NavigationScreen::render();
    echo '<a href="">+ 添加分组</a> | <a href="">+ 添加页面</a> | <a href="">+ 添加内容</a>';
    echo '<div class="abstract">'.$book['abstract'].'</div><a href="">编辑</a>';
    echo '<div class="content">'.$book['content'].'</div><a href="/article-', $GLOBALS['ARTICLE_ID'], '/edit">编辑</a>';
    echo '<p>广告</p>';
  }
}