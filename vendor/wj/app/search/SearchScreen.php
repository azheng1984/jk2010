<?php
class SearchScreen extends Screen {
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
    echo '<div id="total">找到 '.$result['total'].' 个产品</div>';
    echo '</div>';
    echo '<ol>';
//    foreach ($items as $item) {
//      $name = $item['title'].' '.$this->category['name'];
//      echo '<li><div class="image"><a target="_blank" href="/'.$item['id'].'"><img alt="'.$name.'" src="http://img.workbench.wj.com/'.$item['id'].'.jpg" /></a></div><div class="title"><a target="_blank" href="/'.$item['id'].'">'
//        .$name.'</a></div><div class="data"><div>&yen;<span class="price">'.$item['lowest_price'].'</span> ~ <span class="price">12345</span></div> <div>京东商城</div></div></li>';
//    }
    echo '</ol>';
    $this->renderPagination($result['total']);
    echo '</div>';
  }

  private function renderFilter() {
    echo '<div id="filter"><div class="head"><span>分类</span></div></div>';
  }

  private function search() {
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
  }

  private function renderPagination($total) {
    if ($total <= 20) {
      return;
    }
    echo '<div id="pagination"> ';
    $pagination = new Pagination;
    $prefix = preg_replace('{[&?]*page=[0-9]+}', '', $_SERVER['QUERY_STRING']);
    $pagination->render($prefix, $total, 16);
    echo '</div>';
  }

  private function renderAdvertisement() {
    echo '<div id="bottom_ads_wrapper"><div id="bottom_ads">';
    //AdSenseScreen::render(true);
    echo '</div></div>';
  }
}