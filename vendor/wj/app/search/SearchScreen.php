<?php
class SearchScreen extends Screen {
  private $query;
  private $page = 1;
  private $category = false;
  private $key = false;
  private $properties = array();
  private $sort = 'sale_rank';
  private $basePaginationUri;
  private $baseSortUri;
  private $baseQueryUri;

  public function __construct() {
    $arguments = SearchUri::parse();
    $this->query = urldecode($arguments['query']);
    if (isset($arguments['category'])) {
      $this->category = DbCategory::getByName(urldecode($arguments['category']));
    }
    if ($this->category && isset($_GET['t'])) {
      $this->key = DbProperty::getKeyByName($this->category['id'], $_GET['t']);
    }
    if (isset($_GET['page'])) {
      $this->page = $_GET['page'];
    }
    if (isset($_GET['sort'])) {
      if ($_GET['sort'] === '价格') {
        $this->sort = 'lowest_price_x_100';
      }
    }
    $this->buildProperties();
    $current = $this->buildRawUri();
    if ($current !== $_SERVER['REQUEST_URI']) {
      //echo $_SERVER['REQUEST_URI'].'<br />';
      //echo $current;
    }
  }

  private function buildProperties() {
    foreach ($_GET as $keyName => $valueName) {
      if ($keyName === 'page' || $keyName === 'sort') {
        continue;
      }
      $key = DbProperty::getKeyByName($this->category['id'], $keyName);
      $value = DbProperty::getValueByName($key['id'], $valueName);
      $this->properties[] = array('key' => $key, 'value' => $value);
    }
  }

  private function buildRawUri() {
    $result = '/'.urlencode($this->query).'/';
    $this->baseQueryUri = $result;
    if ($this->category !== false) {
      $result .= urlencode($this->category['name']).'/';
    }
    foreach ($this->properties as $item) {
      $result .= '&'.urlencode($item['key']['key']).'='.urlencode($item['value']['value']);
    }
    if (count($this->properties) > 0) {
      $result .= '/';
    }
    $this->baseSortUri = $result;
    if (isset($_GET['sort'])) {
      $result .= '&sort='.urlencode($_GET['sort']);
    }
    $this->basePaginationUri = $result;
    if (isset($_GET['page'])) {
      $result .= '&page='.$_GET['page'];
    }
    return $result;
  }

  public function renderBodyContent() {
    $this->renderAdvertisement(true);
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
    if ($this->query !== '') {
      if ($this->category !== false) {
        echo '<div id="h1_wrapper"><h1><a href="..">'.$this->query;
        echo '</a><span>&rsaquo;</span>'.$this->category['name'];
      } else {
        echo '<div id="h1_wrapper"><h1>'.$this->query;
      }
      echo '</h1></div>';
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
    echo '<div id="result">';
    echo '<h2>';
    echo '<div id="sort">排序: <span>销量</span>'
      .' <a rel="nofollow" href="'.$this->baseSortUri.'?sort=新品">新品</a>'
      .' <a rel="nofollow" href="'.$this->baseSortUri.'?sort=价格">价格</a>'
      .' <a rel="nofollow" href="'.$this->baseSortUri.'?sort=折扣">折扣</a>'
      .'</div>';
    $result = $this->search();
    echo '<div id="total">找到 '.$result['total_found'].' 个产品</div>';
    echo '</h2>';
    echo '<ol>';
//    print_r($result['matches']);
//    $result = $result['matches'];
    $items = array();
    foreach ($result['matches'] as $id => $content) {
      $items[] = DbProduct::get($id);
    }
    foreach ($items as $item) {
      $name = $item['title'];
      //$title = str_replace($this->query, '<em>'.$this->query.'</em>', $item['title']);
      $title = str_replace($this->query, '<em>'.$this->query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($item['title'], ENT_QUOTES, 'utf-8'), 0, 40, 'utf-8'), ENT_QUOTES, 'utf-8'));
      //$description = str_replace($this->query, '<em>'.$this->query.'</em>', mb_substr($item['description'], 0, 64, 'utf-8'));
      $description = str_replace($this->query, '<em>'.$this->query.'</em>', htmlspecialchars(mb_substr(html_entity_decode($item['description'], ENT_QUOTES, 'utf-8'), 0, 64, 'utf-8'), ENT_QUOTES, 'utf-8'));
      echo '<li><div class="image"><a rel="nofollow" target="_blank" href="/r/'.$item['id'].'"><img alt="'.$name.'" src="http://img.wj.com/'.$item['id'].'.jpg" /></a></div><h3><a rel="nofollow" target="_blank" href="/r/'.$item['id'].'">'
        .$title.'</a></h3><div class="price">&yen;<span>'.($item['lowest_price_x_100']/100).'</span></div><p>'.$description.'&hellip;</p> <div class="merchant">京东商城</div></li>';
    }
    echo '</ol>';
    $this->renderPagination($result['total_found']);
    echo '</div>';
  }

  private function renderFilter() {
    echo '<div id="filter"><h2>';
    if ($this->category === false) {
      echo '<div id="breadcrumb">标签: <span class="first">分类</span></div>';
    } elseif ($this->key === false) {
      echo '<div id="breadcrumb">标签: <a class="first" href="'.$this->baseQueryUri.'">分类</a> &rsaquo; <span>'.$this->category['name'].'</span></div>';
    } else {
      echo '<div id="breadcrumb">标签: <a class="first" href="'.$this->baseQueryUri.'">分类</a> &rsaquo; <a href="'.$this->basePaginationUri.'">'.$this->category['name'].'</a> &rsaquo; <span>'.$this->key['key'].'</span></div>';
    }
    echo '</h2>';
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
      echo '<li><a href="'.$this->baseQueryUri.''.$category['name'].'/">'.$category['name'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
    }
    echo '</ol>';
  }

  private function renderKeys() {
    $properies = $this->getKeys();
    echo '<ol id="key_list">';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByKeyId($item['attrs']['@groupby']);
      echo '<li><span>+</span><a href="'.$this->baseSortUri.'?t='.$property['key'].'">'.$property['key'].'</a></li>';
    }
    echo '</ol>';
  }

  private function renderValues() {
    $properies = $this->getValues();
    echo '<ol>';
    foreach ($properies['matches'] as $item) {
      $property = DbProperty::getByValueId($item['attrs']['@groupby']);
      echo '<li><a href="'.$this->baseSortUri.$this->key['key'].'='.$property['value'].'/">'.$property['value'].'</a> <span>'.$item['attrs']['@count'].'</span></li>';
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
    $query = $this->query;
    return $sphinx->query($this->query, 'wj_search');
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
    return $s->query($this->query, 'wj_search');
  }

  private function getValues() {
    $s = new SphinxClient;
    //$offset = ($this->page - 1) * 20;
    //$s->SetLimits($offset, 20);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetFilter('category_id', array($this->category['id']));
    $s->SetFilter('key_id_list', array($this->key['id']));
    $s->SetGroupBy('value_id_list_'.$this->key['mva_index'], SPH_GROUPBY_ATTR, '@count DESC');
    //$s->SetGroupBy('value_id_list'.$this->key['search_field_index'], SPH_GROUPBY_ATTR, '@count DESC');
    $s->SetArrayResult (true);
    return $s->query($this->query, 'wj_search');
  }

  private function search() {
    $s = new SphinxClient;
    $offset = ($this->page - 1) * 16;
    $s->SetLimits($offset, 16);
    $s->setServer("localhost", 9312);
    $s->setMaxQueryTime(30);
    $s->SetSortMode(SPH_SORT_ATTR_ASC, $this->sort);
    if ($this->category !== false) {
      $s->SetFilter ('category_id', array($this->category['id']));
    }
    if (count($this->properties) !== 0) {
      foreach ($this->properties as $item) {
        $s->SetFilter('value_id_list_'.$item['key']['mva_index'], array($item['value']['id']));
      }
    }
    $query = $this->query;
    return $s->query($query, 'wj_search');
  }

  private function renderPagination($total) {
    if ($total <= 20) {
      return;
    }
    echo '<div id="pagination"> ';
    $pagination = new Pagination;
    $prefix = preg_replace('{[&?]*page=[0-9]+}', '', $_SERVER['QUERY_STRING']);
    $pagination->render($this->basePaginationUri.'?', $total, $this->page);
    echo '</div>';
  }

  private function renderAdvertisement($isTop = false) {
    if ($isTop) {
      echo '<div id="top_ads_wrapper"><div id="bottom_ads">';
    } else {
      echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    }
    AdSenseScreen::render(true);
    echo '</div></div>';
  }
}