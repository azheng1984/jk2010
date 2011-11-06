<?php
class SearchScreen extends Screen {
  public function renderBodyContent() {
    $this->renderProductList();
  }

  public function renderHeadContent() {
    echo '<title>货比万家</title>';
    echo '<link type="text/css" href="/css/product_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link type="text/css" href="/css/category_list.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  private function renderProductList() {
    echo '<div id="h1_wrapper"><h1>'.$_GET['q'].'</h1>';
    echo '</div>';
//    $s = new SphinxClient;
//    $offset = ($this->page - 1) * 20;
//    $s->SetLimits($offset, 20);
//    $s->setServer("localhost", 9312);
//    $s->setMaxQueryTime(30);
//    if (empty($_GET['q'])) {
//      $s->setMatchMode(SPH_MATCH_EXTENDED);
//      if (count($valueIds) !== 0) {
//        $result = $s->query(implode(',', $valueIds));
//      } else {
//        $result = $s->query('');
//      }
//    } else {
//      $s->setMatchMode(SPH_MATCH_EXTENDED);
//      $query = '@keywords "'.$_GET['q'].'"';
//      $result = $s->query($query, 'test1');
//    }
//    $items = array();
//    if (isset($result['matches'])) {
//      foreach ($result['matches'] as $id => $value) {
//        $items[] = DbProduct::get($id);
//      }
//    }
    $result = array('total' => '12345');
    echo '<div id="list">';
    echo '<div id="sort">排序: <span>销量</span> <a rel="nofollow" href=".">价格</a> <a href="." rel="nofollow">降价</a></div>';
    echo '<div id="total">找到 '.$result['total'].' 个产品</div>';
    echo '</div>';
    echo '<div id="property_filter">分类</div>';
    echo '<div id="product_list_wrapper"><ol id="product_list">';
//    foreach ($items as $item) {
//      $name = $item['title'].' '.$this->category['name'];
//      echo '<li><div class="image"><a target="_blank" href="/'.$item['id'].'"><img alt="'.$name.'" src="http://img.workbench.wj.com/'.$item['id'].'.jpg" /></a></div><div class="title"><a target="_blank" href="/'.$item['id'].'">'
//        .$name.'</a></div><div class="data"><div>&yen;<span class="price">'.$item['lowest_price'].'</span> ~ <span class="price">12345</span></div> <div>京东商城</div></div></li>';
//    }
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
    $pagination->render($prefix, $total, 1, $pageOne);
    echo '</div>';
  }
}