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
    echo $this->category['name'],'品牌';
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
    echo $this->category['name'], '品牌</h1>';
    $this->printChildren();
    echo '<div><a href="/category/new?parent_id=',$this->category['id'],'">添加分类</a></div>';
    echo '<div><a href="/category-'.$this->category['id'].'/edit">修改</a></div>';
    echo '<div><a href="/brand/new?category_id=',$this->category['id'],'">添加品牌</a></div>';
    if ($this->category['brand_amount'] === '0') {
      echo '<script>function hi() { window.location = $.ajax({url:"", async:false, type:"DELETE"}).responseText; }</script>';
      echo '<div><button onclick="hi()">删除分类</button></div>';
    }
    echo '<h2><a href="top/">十大', $this->category['name'], '品牌排名</a>（已有 23 人参与活动）</h2>';
    $list = Db::getAll(
      'SELECT b.*,bc.popularity_rank FROM brand_category'
        .' bc LEFT JOIN brand b ON bc.brand_id = b.id'
        .' WHERE bc.category_id = ? ORDER BY bc.popularity_rank desc LIMIT 0, 50',
      $this->category['id']
    );
    echo '<ul>';
    ?>
    <script>
    function updatePr(id) {
      var a = $('#bpr' + id).val();
      //$.ajax('/category-brand/', {'type':'PUT'});
      //alert('ji');
      $.ajax({url:'brand?brand_id=' + id, type:'POST', data: $('#f' + id).serialize(), async:false}); 
      window.location.reload();
      //alert('ji');
      return false;
    }
    </script>
    <?php
    foreach ($list as $item) {
      echo '<li>';
      echo '<a class="title" href="/brand-', $item['id'], '/">', $item['name'], '</a>';
      echo '<div>', $item['abstract'], '</div>';
      echo '<div>PR:<form id="f', $item['id'], '" onsubmit="return updatePr(', $item['id'], ')"><input name="_method" value="PUT" type="hidden"/><input id="bpr', $item['id'], '" name="pr" value="', $item['popularity_rank'], '"/></form></div>';
      echo '<div>{ ', $item['page_view'], ' }次浏览 源自：广州</div>';
      echo '</li>';
    }
    echo '</ul>';
    PaginationScreen::render(intval($this->page), 1000);
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
    if (count($categoryList) === 0 && $this->category['other_brand_amount'] !== '0') {
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
      'SELECT * FROM category WHERE parent_id = ? AND is_active = 1'
        .' ORDER BY popularity_rank DESC',
      $this->category['id']
    );
    echo '<ul id="category_list">';
    foreach ($categoryList as $category) {
      echo '<li><a href="/category-', $category['id'], '/">', $category['name'], '</a> ';
      if ($category['brand_amount'] === '0') {
        echo '删除';
      }
      echo '</li>';
    }
    if ($this->category['is_leaf'] === false && $this->category['article_amount'] !== 0) {
      
    }
    if ($this->category['other_brand_amount'] !== '0') {
      echo '<li><a href="/category-',$this->category['id'],'-other/">其他</a></li>';
      echo '</ul>';
    }
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