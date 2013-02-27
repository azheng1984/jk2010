<?php
class CategoryScreen extends Screen {
  protected function renderHtmlHeadContent() {
    echo '<title>优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="category" class="content">';
    $this->printBreadcrumb();
    echo '<h1>', $GLOBALS['CATEGORY']['name'], '</h1>';
    $this->printChildren();
    $orderBy = null;
    if (isset($_GET['sort']) && $_GET['sort'] === 'time') {
      $orderBy = 'creation_time';
      echo '<div id="sort">排序：<a href=".">热门</a> | <strong>创建时间</strong></div>';
    } else {
      $orderBy = 'popularity_rank';
      echo '<div id="sort">排序：<strong>热门</strong> | <a href="?sort=time">创建时间</a></div>';
    }
    $list = Db::getAll(
      'SELECT a.* FROM article_category AS ac LEFT JOIN article AS a ON ac.article_id = a.id WHERE ac.category_id = ? ORDER BY ac.'.$orderBy,
      $GLOBALS['CATEGORY']['id']
    );
    echo '<ol>';
    foreach ($list as $item) {
      echo '<li class="article">';
      echo '<a class="title" href="/article-', $item['id'], '/">', $item['title'], '</a>';
      echo '<div>', $item['abstract'], '</div>';
      DbConnection::connect('youxuanji');
      $userName = Db::getColumn('SELECT name FROM user WHERE id = ?', $item['user_id']);
      echo '<div><img src="/asset/img/avatar_small.jpg" /> { <a href="/user-', $item['user_id'], '/">', $userName, '</a> }</div>';
      echo '<div>喜欢 { ', $item['like_amount'], ' }</div>';
      echo '<div>关注 { ', $item['watch_amount'], ' }</div>';
      echo '<div>创建 { ', $item['creation_time'], ' }</div>';
      echo '<div>更新 { ', $item['modification_time'], ' }</div>';
      DbConnection::close();
      echo '</li>';
    }
    $tmp = '';
    if ($orderBy === 'creation_time') {
      $tmp = '?sort=time';
    }
    echo '</ol>';
    PaginationScreen::render('1', '100', '/category-2323/', $tmp);
    echo '</div>';
  }

  private function printChildren() {
    DbConnection::connect('youxuanji');
    $categoryList = Db::getAll('SELECT * FROM category WHERE parent_id = ?', $GLOBALS['CATEGORY']['id']);
    DbConnection::close();
    echo '<div id="category_list">';
    foreach ($categoryList as $category) {
      echo '<p><a href="/category-', $category['id'], '/">', $category['name'], '</a></p>';
    }
    if ($GLOBALS['CATEGORY']['is_leaf'] === false && $GLOBALS['CATEGORY']['article_amount'] !== 0) {
      echo '<p><a href="other">其他</a></p>';
    }
    echo '</div>';
  }

  private function printBreadcrumb() {
    $categoryList = array();
    $id = $GLOBALS['CATEGORY']['parent_id'];
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
      echo '<a href ="/category-'.$category['id'].'/">'.$category['name'].'</a> &gt; ';
    }
    echo $GLOBALS['CATEGORY']['name'];
  }
}