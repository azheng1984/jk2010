<?php
class CategoryScreen extends Screen {
  private $category;

  public function __construct() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new NotFoundException;
    }
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    $this->category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
    if ($this->category === false) {
      throw new NotFoundException;
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title> - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
//     
//     exit;
//     //if (is_array($var))
//     exit;
    echo '<div id="category" class="content">';
    $this->printBreadcrumb();
    echo '<h1>', $this->category['name'], '</h1>';
    $this->printChildren();
    echo '攻略 | 讨论';
    $orderBy = null;
    if (isset($_GET['sort']) && $_GET['sort'] === 'time') {
      $orderBy = 'creation_time';
      echo '<div id="sort">排序：<a href=".">热门</a> | <strong>创建时间</strong></div>';
    } else {
      $orderBy = 'popularity_rank';
      echo '<div id="sort">排序：<strong>热门</strong> | <a href="?sort=time">创建时间</a></div>';
    }
    exit;
    $page = 1;
    if ($GLOBALS['PATH_SECTION_LIST'][2] !== '') {
      $page = $GLOBALS['PATH_SECTION_LIST'][2];
    }
    $list = Db::getAll(
      'SELECT a.* FROM article_category'
        .' AS ac LEFT JOIN article AS a ON ac.article_id = a.id'
        .' WHERE ac.category_id = ? ORDER BY ac.'.$orderBy
        .' LIMIT 0, 25',
      $GLOBALS['CATEGORY']['id']
    );
    echo '<ol>';
    foreach ($list as $item) {
      echo '<li class="article">';
      echo '<a class="title" href="/article-', $item['id'], '/">', $item['title'], '</a>';
      echo '<div>', $item['abstract'], '</div>';
      DbConnection::connect('youxuanji');
      $userName = Db::getColumn('SELECT name FROM user WHERE id = ?', $item['user_id']);
      echo '<div><img src="/asset/img/avatar_small.jpg" /><a href="/user-', $item['user_id'], '/">', $userName, '</a>', $item['creation_time'], '</div>';
      echo '<div>喜欢 { ', $item['like_amount'], ' }</div>';
      echo '<div>关注 { ', $item['watch_amount'], ' }</div>';
      echo '<div>浏览 { ', $item['page_view'], ' }</div>';
      echo '<div>更新 { ', $item['modification_time'], ' }</div>';
      DbConnection::close();
      echo '</li>';
    }
    $tmp = '';
    if ($orderBy === 'creation_time') {
      $tmp = '?sort=time';
    }
    echo '</ol>';
    PaginationScreen::render(intval($page), 1000, '/category-'.$GLOBALS['CATEGORY']['id'].'/', $tmp);
    echo '</div>';
  }

  private function printChildren() {
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = ?'
        .' ORDER BY popularity_rank DESC',
      $this->category['id']
    );
    echo '<ul id="category_list">';
    foreach ($categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '">', $category['name'], '</a></li>';
    }
    if ($this->category['is_leaf'] === false && $this->category['article_amount'] !== 0) {
      echo '<li><a href="other">其他</a></li>';
    }
    echo '</ul>';
  }

  private function printBreadcrumb() {
    $categoryList = array();
    $id = $this->category['parent_id'];
    while ($id !== '0') {
      $category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
      array_unshift($categoryList, $category);
      $id = $category['parent_id'];
    }
    $list = array();
    echo '<a href="/">首页</a> › ';
    foreach ($categoryList as $category) {
      echo '<a href ="/category-', $category['id'], '/">',
        $category['name'], '</a> › ';
    }
    echo $this->category['name'];
  }
}