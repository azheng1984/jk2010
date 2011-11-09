<?php
class SearchScreen extends Screen {
  private $page = 1;
  private $category = false;
  private $key = false;
  private $properties = array();
  private $sort = 'sale_rank';

  public function __construct() {
    if (isset($_GET['c'])) {
      $this->category = DbCategory::getByName($_GET['c']);
    }
    if ($this->category && isset($_GET['p'])) {
      $this->key = DbProperty::getKeyByName($this->category['id'], $_GET['p']);
    }
  }

  public function renderBodyContent() {
    //$this->renderAdvertisement();
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

      //$title = str_replace($_GET['q'], '<em>'.$_GET['q'].'</em>', $item['title']);
      $title = str_replace($_GET['q'], '<em>'.$_GET['q'].'</em>', htmlspecialchars(mb_substr(html_entity_decode($item['title'], ENT_QUOTES, 'utf-8'), 0, 40, 'utf-8'), ENT_QUOTES, 'utf-8'));
      //$description = str_replace($_GET['q'], '<em>'.$_GET['q'].'</em>', mb_substr($item['description'], 0, 64, 'utf-8'));
      $description = str_replace($_GET['q'], '<em>'.$_GET['q'].'</em>', htmlspecialchars(mb_substr(html_entity_decode($item['description'], ENT_QUOTES, 'utf-8'), 0, 64, 'utf-8'), ENT_QUOTES, 'utf-8'));
      echo '<li><div class="image"><a target="_blank" href="/'.$item['id'].'"><img alt="'.$name.'" src="http://img.wj.com/'.$item['id'].'.jpg" /></a></div><div class="title"><a target="_blank" href="/'.$item['id'].'">'
        .$title.'</a></div><div class="data"><div>&yen;<span class="price">'.($item['lowest_price_x_100']/100).'</span></div><div class="description">'.$description.'&hellip;</div> <div class="merchant_name">京东商城</div></div></li>';
    }
    echo '</ol>';
    $this->renderPagination($result['total_found']);
    echo '</div>';
  }

  private function renderFilter() {
    echo '<div id="filter"><div class="head">';
    if ($this->category === false) {
      echo '<div id="breadcrumb"><span class="first">分类</span></div>';
    } elseif ($this->key === false) {
      echo '<div id="breadcrumb"><a class="first" href="javascript:void(0)">分类</a> &rsaquo; <span>'.$this->category['name'].'</span></div>';
    } else {
      echo '<div id="breadcrumb"><a class="first" href="javascript:void(0)">分类</a> &rsaquo; <a href="javascript:void(0)">'.$this->category['name'].'</a> &rsaquo; <span>'.$this->key['key'].'</span></div>';
    }
    echo '</div>';
    if ($this->category === false) {
      $this->renderCategories();
    } elseif ($this->key === false) {
      $this->renderKeys();
    } else {
      $this->renderValues();
    }
    echo '</div>';
  }

  private function renderCategories() {
    $categories = $this->getCategories();
    echo '<ol>';
    foreach ($categories['matches'] as $item) {
      $category = DbCategory::get($item['attrs']['@groupby']);
      echo '<li><a href="?c='.$category['name'].'">'.$category['name'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }

  private function renderKeys() {
    $properies = $this->getKeys();
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByKeyId($item['attrs']['@groupby']);
      echo '<li><span>+</span><a href="?c='.$property['key'].'">'.$property['key'].'</a></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = $this->getValues();
    echo '<ol>';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="?c='.$property['value'].'">'.$property['value'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
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

  private function getKeys() {
    $s = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetFilter ('category_id', array($this->category['id']));
    $s->SetGroupBy('key_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    $s->SetArrayResult (true);
    return $s->query($_GET['q'], 'wj_search');
  }

  private function getValues() {
    $s = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetFilter('category_id', array($this->category['id']));
    $s->SetFilter('key_id_list', array($this->key['id']));
    $s->SetGroupBy('value_id_list', SPH_GROUPBY_ATTR, '@count DESC');
    //$s->SetGroupBy('value_id_list'.$this->key['search_field_index'], SPH_GROUPBY_ATTR, '@count DESC');
    $s->SetArrayResult (true);
    return $s->query($_GET['q'], 'wj_search');
  }

  private function search() {
    $s = new SphinxClient;
//    $offset = ($this->page - 1) * 20;
    $s->SetLimits(0, 16);
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
    ///AdSenseScreen::render(true);
    echo '</div></div>';
  }
}