<?php
class SearchScreen extends Screen {
  public function renderContent() {
    $categories = array();
    $category = null;
    $parentId = 0;
    foreach ($_GET['categories'] as $categoryName) {
      if ($categoryName !== '') {
        $category = DbCategory::getByName(urldecode($categoryName), $parentId);
        if ($category === false) {
          throw new NotFoundException;
        }
        $categories[] = $category;
        $parentId = $category['id'];
        if ($category['table_prefix'] !== null) {
          break;
        }
      }
    }
    $breadcrumb = new Breadcrumb($categories);
    $breadcrumb->render();
    echo '<div id="product_search">';
    echo '<div id="category_title"><h1>'.$category['name'].'</h1></div>';
    if ($category['table_prefix'] === null) {
      echo '<div class="list_wrapper"><ul id="category_list">';
      for ($i = 0; $i < 5; ++$i) {
        foreach (DbCategory::getList($parentId) as $category) {
          echo '<li><div class="bull">&bull;</div><h2><a href="'.urlencode($category['name']).'/">'
            .$category['name'].'</a></h2><div class="children"><a href="/">电脑</a> <a href="/">手机</a> &hellip;</div></li>';
        }
      }
      echo '</ul></div>';
      echo '<div class="ads">Google 提供的广告</div>';
      return;
    }
    $this->renderProductList($category);
  }

  private function renderProductList($category) {
    $filter = new FilterScreen;
    $filter->render($category);
    $valueIds = array();
    foreach (FilterParameter::getSelectedList($category) as $item) {
      foreach ($item[1] as $value) {
        $valueIds[] = $value['id'];
      }
    }
    echo '<div class="reset"></div>';
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
    echo '<div id="sort_box">';
    echo '<div class="sort">排序: <span class="selected">销量</span> <a rel="nofollow" href="/">新品</a> <a href="/" rel="nofollow">降价</a> <a rel="nofollow" href="/">价格</a></div>';
    echo '<div class="total_record">找到 '.$amount.' 件产品 <span class="reset"><a rel="nofollow" href=".">重新筛选</a></span></div>';
    echo '</div>';
    echo '<ul id="product_list">';
    for ($index = 0; $index < 5; $index++) {
      foreach ($items as $item) {
        echo '<li class="';
        if ($index === 1) {
          echo 'visited ';
        }
        echo 'item"><div class="product_image"><a target="_blank" href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a  target="_blank" href="/'.$item['id'].'">'
          .$item['name'].'</a></h2><div class="price_block"><span class="rmb">&yen;</span><span class="price">10000<span class="point">.68</span></span> &#8764; <span class="price">1234567890<span class="point"></span></span> <div>7 个商城</div></div></li>';
      }
    }
    echo '</ul>';
    echo '<div class="pagination"> <span href="/" class="current_page">1</span> <a href="/">2</a> <a href="/">下一页 &raquo;</a></div>';
    echo '<div class="ads">Google 提供的广告</div>';
    echo '</div>'; //end of search
  }
}
//anchnet-jh#12.200