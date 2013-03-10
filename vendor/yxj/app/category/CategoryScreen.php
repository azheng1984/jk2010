<?php
class CategoryScreen extends Screen {
  private $category;
  private $baseHref;
  private $sort;
  private $page = 1;
  private $isOther;

  public function __construct() {
    if (!is_array($GLOBALS['PATH_SECTION_LIST'][1])) {
      throw new NotFoundException;
    }
    $id = $GLOBALS['PATH_SECTION_LIST'][1][1];
    if (strpos($id, '-other') !== false) {
      $id = substr($id, 0, strlen($id) - 6);
      $this->isOther = true;
    }
    $this->category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
    if ($this->category === false) {
      throw new NotFoundException;
    }
    $sort = null;
    $this->baseHref = '/category-'.$this->category['id'].'/';
    if ($this->isOther) {
      $this->baseHref = '/category-'.$this->category['id'].'-other/';
    }
    if (isset($_GET['sort'])) {
      $this->sort = 'time';
      $sort = 'creation_time';
      if (isset($_GET['page'])) {
        $this->page = intval($_GET['page']);
      }
    } else {
      $sort = 'popularity_rank';
      if (ctype_digit($GLOBALS['PATH_SECTION_LIST'][2])) {
        $this->page = intval($GLOBALS['PATH_SECTION_LIST'][2]);
        if ($this->page !== 1) {
          $this->baseHref .= $this->page;
        }
      }
    }
    if ($this->page < 1) {
      $page = 1;
    }
    $start = ($this->page - 1) * 25;
    $list = Db::getAll(
      'SELECT a.* FROM article_category'
        .' AS ac LEFT JOIN article AS a ON ac.article_id = a.id'
        .' WHERE ac.category_id = ? ORDER BY ac.'.$sort
        .' LIMIT '.$start.', 25',
      $this->category['id']
    );
    if ($GLOBALS['PATH'] !== $this->baseHref) {
      $this->stop();
      header(
        'Location: http://'.$_SERVER['SERVER_NAME'].$this->baseHref
      );
      header('HTTP/1.1 301 Moved Permanently');
    }
  }

  protected function renderHtmlHeadContent() {
    echo '<title>';
    if ($this->isOther) {
      echo '其他';
    }
    echo $this->category['name'],'攻略';
//     if ($this->sort === 'time') {
//       echo ' - 创建时间';
//     } else {
//       echo ' - 热门';
//     }
    if ($this->page !== 1) {
      echo ' - 第',$this->page,'页';
    }
    echo ' - 优选集</title>';
  }

  protected function renderHtmlBodyContent() {
    echo '<div id="category" class="content">';
    $this->printBreadcrumb();
    echo '<h1>';
    if ($this->isOther) {
      echo '其他';
    }
    echo $this->category['name'], '攻略</h1>';
    $this->printChildren();
    echo '攻略 | <a href="discussion/">讨论</a>';
    $orderBy = null;
    if (isset($_GET['sort']) && $_GET['sort'] === 'time') {
      $orderBy = 'creation_time';
      echo '<div id="sort">排序：<a href=".">热门</a> | <strong>创建时间</strong></div>';
    } else {
      $orderBy = 'popularity_rank';
      echo '<div id="sort">排序：<strong>热门</strong> | <a href=".?sort=time" rel="nofollow">创建时间</a></div>';
    }
    $page = 1;
    if ($GLOBALS['PATH_SECTION_LIST'][2] !== '') {
      $page = $GLOBALS['PATH_SECTION_LIST'][2];
    }
    $list = Db::getAll(
      'SELECT a.* FROM article_category'
        .' ac LEFT JOIN article a ON ac.article_id = a.id'
        .' WHERE ac.category_id = ? ORDER BY ac.'.$orderBy
        .' LIMIT 0, 25',
      $this->category['id']
    );
    echo '<ol>';
    foreach ($list as $item) {
      echo '<li class="article">';
      echo '<a class="title" href="/article-', $item['id'], '/">', $item['title'], '</a>';
      echo '<div>', $item['abstract'], '</div>';
      $userName = Db::getColumn('SELECT name FROM user WHERE id = ?', $item['user_id']);
      echo '<div><a href="/user-', $item['user_id'], '/"><img src="/asset/image/avatar_small.jpg" />', $userName, '</a>', $item['creation_time'], '</div>';
      echo '<div>喜欢 { ', $item['like_amount'], ' }</div>';
      echo '<div>关注 { ', $item['watch_amount'], ' }</div>';
      echo '<div>浏览 { ', $item['page_view'], ' }</div>';
      echo '<div>更新 { ', $item['modification_time'], ' }</div>';
      if ($this->isOther !== true) {
        echo '<div>来自子分类: ';
        $this->printArticleCategory($item['category_id']);
        echo '</div>';
      }
      echo '</li>';
    }
    $tmp = '';
    if ($orderBy === 'creation_time') {
      $tmp = '?sort=time&page=';
    }
    echo '</ol>';
    if ($this->sort === null) {
      PaginationScreen::render(intval($this->page), 1000);
    } else {
      PaginationScreen::render(intval($this->page), 1000, $tmp, '', '?sort=time');
    }
    echo '</div>';
  }

  private function printArticleCategory($id) {
    $categoryList = array();
    while ($id !== $this->category['id']) {
      $category = Db::getRow('SELECT * FROM category WHERE id = ?', $id);
      array_unshift($categoryList, $category);
      $id = $category['parent_id'];
    }
    if (count($categoryList) === 0 && $this->isOther) {
      return;
    }
    if (count($categoryList) === 0) {
      echo '<a href ="/category-', $id, '-other/">其他</a>';
      return;
    }
    $list = array();
    foreach ($categoryList as $category) {
      echo '<a href ="/category-', $category['id'], '/">',
      $category['name'], '</a> › ';
    }
  }

  private function printChildren() {
    if ($this->isOther || $this->category['is_leaf'] === '1') {
      return;
    }
    $categoryList = Db::getAll(
      'SELECT * FROM category WHERE parent_id = ?'
        .' ORDER BY popularity_rank DESC',
      $this->category['id']
    );
    echo '<ul id="category_list">';
    foreach ($categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/">', $category['name'], '</a></li>';
    }
    if ($this->category['is_leaf'] === false && $this->category['article_amount'] !== 0) {
      
    }
    echo '<li><a href="/category-',$this->category['id'],'-other/">其他</a></li>';
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
    if ($this->isOther) {
      echo '<a href ="/category-', $this->category['id'], '/">',
      $this->category['name'], '</a> › <a href=".">其他</a>';
      return;
    }
    echo '<a href =".">'.$this->category['name'].'</a>';
  }
}