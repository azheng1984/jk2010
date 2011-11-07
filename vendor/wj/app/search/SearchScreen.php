<?php
class SearchScreen extends Screen {
  private $page = 1;
  private $category = false;
  private $properties = array();
  private $sort = 'sale_rank';

  public function __construct() {
    if (isset($_GET['分类'])) {
      $this->category = DbCategory::getByName($_GET['分类']);
    }
  }

  public function renderBodyContent() {
    $this->renderTitle();
    $this->renderSearch();
    $this->renderAdvertisement();
  }

  public function renderHeadContent() {
    echo '<title>货比万家</title>';
    echo '<link type="text/css" href="/css/search.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  private function renderTitle() {
    if ($_GET['q'] !== '') {
      echo '<div id="h1_wrapper"><h1>'.$_GET['q'].'</h1></div>';
    }
  }

  private function renderSearch() {
    $result = array('total' => '12345');
    echo '<div id="search">';
    $this->renderResult();
    $this->renderFilter();
    echo '</div>';
  }

  private function renderResult() {
    $result = array('total' => 12345);
    echo '<div id="result">';
    echo '<div class="head">';
    echo '<div id="sort">排序: <span>销量</span>'
      .' <a rel="nofollow" href="javascript:void(0)">价格</a>'
      .' <a href="." rel="nofollow">降价</a>'
      .'</div>';
    $result = $this->search();
    echo '<div id="total">找到 '.$result['total_found'].' 个产品</div>';
    echo '</div>';
    echo '<ol>';
//    print_r($result['matches']);
//    $result = $result['matches'];
    $items = array();
    foreach ($result['matches'] as $id => $content) {
      $items[] = DbProduct::get($id);
    }
    foreach ($items as $item) {
      $name = $item['title'];
      echo '<li><div class="image"><a target="_blank" href="/'.$item['id'].'"><img alt="'.$name.'" src="http://img.workbench.wj.com/'.$item['id'].'.jpg" /></a></div><div class="title"><a target="_blank" href="/'.$item['id'].'">'
        .$name.'</a></div><div class="data"><div>&yen;<span class="price">'.$item['lowest_price_x_100'].'</span> ~ <span class="price">1234567890</span></div> <div>京东商城</div></div></li>';
    }
    echo '</ol>';
    $this->renderPagination($result['total_found']);
    echo '</div>';
  }

  private function renderFilter() {
    echo '<div id="filter"><div class="head">';
    if ($this->category === false) {
      echo '<span>分类</span>';
    } else {
      echo '<a href="javascript:void(0)">分类</a> &rsaquo; <span>'.$this->category['name'].'</span>';
    }
    echo '</div>';
    if ($this->category === false) {
      $this->renderCategories();
    } else {
      $this->renderProperties();
    }
    echo '</div>';
  }

  private function renderCategories() {
    $categories = $this->getCategories();
    echo '<ol>';
    foreach ($categories['matches'] as $item) {
      $category = DbCategory::get($item['attrs']['@groupby']);
      echo '<li><a href="?分类:'.$category['name'].'">'.$category['name'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }

  private function renderProperties() {
    $properies = $this->getProperties();
    echo '<ol>';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="?分类:'.$property['value'].'">'.$property['value'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }

  private function getCategories() {
    $sphinx = new SphinxClient;
//    $offset = ($this->page - 1) * 20;
//    $sphinx->SetLimits($offset, 20);
    $sphinx->setServer("localhost", 9312);
    $sphinx->setMaxQueryTime(30);
    $sphinx->SetGroupBy('category_id', SPH_GROUPBY_ATTR, '@count DESC');
    $query = $_GET['q'];
    return $sphinx->query($_GET['q'], 'wj_search');
  }

  private function getProperties() {
    $s = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetFilter ('category_id', array($this->category['id']));
    $s->SetGroupBy('property_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    $s->SetArrayResult (true);
    return $s->query($_GET['q'], 'wj_search');
  }

  private function search() {
    $s = new SphinxClient;
//    $offset = ($this->page - 1) * 20;
//    $s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetSortMode (SPH_SORT_ATTR_DESC, $this->sort);
    if ($this->category !== false) {
      $s->SetFilter ('category_id', array($this->category['id']));
    }
    if (count($this->properties) !== 0) {
      $s->SetFilter('property_id_list', $this->properties);
    }
    $query = $_GET['q'];
    return $s->query($query, 'wj_search');
  }

  private function renderPagination($total) {
    if ($total <= 20) {
      return;
    }
    echo '<div id="pagination"> ';
    $pagination = new Pagination;
    $prefix = preg_replace('{[&?]*page=[0-9]+}', '', $_SERVER['QUERY_STRING']);
    $pagination->render($prefix, $total, 1);
    echo '</div>';
  }

  private function renderAdvertisement() {
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    //AdSenseScreen::render(true);
    echo '</div></div>';
  }
}