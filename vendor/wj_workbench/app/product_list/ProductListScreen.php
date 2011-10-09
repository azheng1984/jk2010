<?php
class ProductListScreen extends Screen {
  private $category;

  public function __construct() {
    $this->category = end($_GET['categories']);
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
    $amount = 0;
    if (isset($result['matches'])) {
      foreach ($result['matches'] as $id => $value) {
        $items[] = DbProduct::get($category['table_prefix'], $id);
      }
      $amount = count($result['matches']);
    }
    echo '<div id="sort_wrapper">';
    echo '<div id="sort">排序: <span>销量</span> <a rel="nofollow" href="/">新品</a> <a href="/" rel="nofollow">降价</a> <a rel="nofollow" href="/">价格</a></div>';
    echo '<div id="total">找到 '.$amount.' 个产品</div>';
    echo '</div>';
    echo '<div id="product_list_wrapper"><ul id="product_list">';
    for ($index = 0; $index < 5; $index++) {
      foreach ($items as $item) {
        echo '<li><div><a target="_blank" href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><div class="title"><a  target="_blank" href="/'.$item['id'].'">'
          .$item['name'].'</a></div><div class="data"><div>&yen;<span class="price">10000.68</span> ~ <span class="price">1234567890</span></div> <div>7 个商城</div></div></li>';
      }
    }
    echo '</ul></div>';
    echo '<div id="pagination"> <span href="/">1</span> <a rel="nofollow" href="/">2</a> <a rel="nofollow" href="/">下一页 &raquo;</a></div>';
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    AdSenseScreen::render();
    echo '</div></div>';
  }
}