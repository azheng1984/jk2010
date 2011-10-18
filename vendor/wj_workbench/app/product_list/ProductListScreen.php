<?php
class ProductListScreen extends Screen {
  private $category;
  private $page = 1;

  public function __construct() {
    $this->category = end($_GET['categories']);
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
      $this->page = $_GET['page'];
    }
  }

  protected function renderHeadContent() {
    echo '<title>'.$this->category['name'].' - 货比万家</title>';
    echo '<link type="text/css" href="/css/home.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/category_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/product_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderBodyContent() {
    $breadcrumb = new Breadcrumb($_GET['categories']);
    $breadcrumb->render();
    $category = $this->category;
    echo '<div id="h1_wrapper"><h1>'.$category['name'].'</h1>';
    if (count($_GET) > 1) {
      echo '<div id="action"><a href=".">重新筛选</a></div>';
    }
    echo '</div>';
    $filter = new FilterScreen;
    $filter->render($category);
    $valueIds = array();
    foreach (FilterParameter::getSelectedList($category) as $item) {
      foreach ($item[1] as $value) {
        $valueIds[] = $value['id'];
      }
    }
    $s = new SphinxClient;
    $offset = ($this->page - 1) * 20;
    $s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    if (empty($_GET['q'])) {
      $s->setMatchMode(SPH_MATCH_EXTENDED);
      if (count($valueIds) !== 0) {
        $result = $s->query(implode(',', $valueIds));
      } else {
        $result = $s->query('');
      }
    } else {
      $s->setMatchMode(SPH_MATCH_EXTENDED);
      $query = '@keyword_list "'.$_GET['q'].'"';
      if (count($valueIds) !== 0) {
        $query .= ' @property_value_list '.implode(',', $valueIds);
      }
      $result = $s->query($query, 'test1');
    }
    $items = array();
    if (isset($result['matches'])) {
      foreach ($result['matches'] as $id => $value) {
        $items[] = DbProduct::get($category['table_prefix'], $id);
      }
    }
    echo '<div id="list">';
    echo '<div id="sort">排序: <span>销量</span> <a rel="nofollow" href=".">新品</a> <a href="." rel="nofollow">降价</a> <a rel="nofollow" href=".">价格</a></div>';
    echo '<div id="total">找到 '.$result['total'].' 个产品</div>';
    echo '</div>';
    echo '<div id="product_list_wrapper"><ol id="product_list">';
    foreach ($items as $item) {
      $name = $item['brand'].' '.$item['model'].' '.$this->category['name'];
      echo '<li><div class="image"><a target="_blank" href="/'.$item['id'].'"><img alt="'.$name.'" src="http://img.workbench.wj.com/'.$item['id'].'.jpg" /></a></div><div class="title"><a target="_blank" href="/'.$item['id'].'">'
        .$name.'</a></div><div class="data"><div>&yen;<span class="price">'.$item['lowest_price'].'</span> ~ <span class="price">12345</span></div> <div>7 个商城</div></div></li>';
    }
    echo '</ol></div>';
    $this->renderPagination($result['total']);
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    AdSenseScreen::render(true);
    echo '</div></div>';
  }

  private function renderPagination($total) {
    if ($total <= 20) {
      return;
    }
    echo '<div id="pagination"> ';
    $pagination = new Pagination;
    $prefix = preg_replace('{[&?]*page=[0-9]+}', '', $_SERVER['QUERY_STRING']);
    $pageOne = $prefix;
    if ($prefix !== '') {
      $prefix = '?'.$prefix.'&';
      $pageOne .= '#list';
    } else {
      $prefix = '?';
      $pageOne = '.#list';
    }
    $pagination->render($prefix, $total, $this->page, $pageOne);
    echo '</div>';
  }
}