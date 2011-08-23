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
    echo '<div id="category_title"><h1>'.$category['name'].'</h1></div>';
    if ($category['table_prefix'] === null) {
      echo '<ul id="category_list">';
      foreach (DbCategory::getList($parentId) as $category) {
        echo '<li><a href="'.urlencode($category['name']).'/">'
          .$category['name'].'</a></li>';
      }
      echo '</ul>';
      return;
    }
    $this->renderProductList($category);
  }

  private function renderProductList($category) {
    $filter = new FilterScreen;
    $filter->render($category);
    $valueIds = array();
    foreach (FilterParameter::getSelectedList() as $item) {
      foreach ($item[1] as $value) {
        $valueIds[] = $value['id'];
      }
    }
    echo '<div class="reset"></div>';
    $s = new SphinxClient;
    $s->setServer("localhost", 9312);
    $s->setMatchMode(SPH_MATCH_ALL);
    $s->setMaxQueryTime(3);
    $result = $s->query(implode(',', $valueIds));
    $items = array();
    $amount = 0;
    if (isset($result['matches'])) {
      foreach ($result['matches'] as $id => $value) {
        $items[] = DbProduct::get('laptop', $id);
      }
      $amount = count($result['matches']);
    }
    echo '<div class="total_record">找到 '.$amount.' 件产品 [ <a href=".">重新筛选</a> ]</div>';
    echo '<ul id="product_list">';
    for ($index = 0; $index < 5; $index++) {
      foreach ($items as $item) {
        echo '<li class="item"><div class="product_image"><a href="/'.$item['id'].'"><img title="'.$item['name'].'" alt="'.$item['name'].'" src="/x.jpg" /></a></div><h2><a href="/'.$item['id'].'">'
          .$item['name'].'</a></h2><div class="price_block"><span class="rmb">￥</span><span class="price">10000<span class="point">.68</span></span> ~ <span class="price">12299<span class="point"></span></span> <div>7 个商城</div></div></li>';
      }
    }
    echo '</ul>';
    echo '<div class="pagination"><a href="/">上一页</a> <span href="/" class="current_page">1</span> <a href="/">2</a> <a href="/">下一页</a></div>';
  }
}