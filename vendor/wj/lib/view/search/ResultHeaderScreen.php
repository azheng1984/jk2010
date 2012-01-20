<?php
class ResultHeaderScreen {
  private static $orderBy = '销量';
  private static $isReverse = false;

  public static function render($amount) {
    if (isset($_GET['sort'])) {
      self::$orderBy = $_GET['sort'];
    }
    if (self::$orderBy === '-价格') {
      self::$orderBy = '价格';
      self::$isReverse = true;
    }
    self::renderTabList($amount);
  }

  private static function renderTabList($amount) {
     echo '<div id="sort">排序: ';
     foreach (array('销量', '上架时间', '折扣', '价格') as $tab) {
       if (self::$orderBy === $tab) {
         if ($tab === '价格') {
           echo '<span class="order_by_price"><span>价格</span>';
           self::renderPriceOrder();
           echo '</span>';
           continue;
         }
         echo '<span>', $tab, '</span>';
         continue;
       }
       echo '<a rel="nofollow" href=".', SearchUriArgument::get($tab), '">', $tab, '</a>';
     }
     echo '</div>';
     self::renderPriceLimit();
     echo '<div id="total_found">找到 ', $amount, ' 个商品</div>';
  }

  private static function renderPriceOrder() {
    if (!self::$isReverse) {
      echo '<strong>低-高</strong><a rel="nofollow" href=".', SearchUriArgument::get('-价格'), '">高-低</a>';
      return;
    }
    echo '<a rel="nofollow" href=".', SearchUriArgument::get('价格'), '">低-高</a><strong>高-低</strong>';
  }

  private static function renderPriceLimit() {
    $priceFrom = isset($_GET['price_from']) ? $_GET['price_from'] : '';
    $priceTo = isset($_GET['price_to']) ? $_GET['price_to'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    echo '<form id="price_limit" action="."><label for="price_from">&yen;</label> ',
      '<input name="sort" type="hidden" value="'.$sort.'" /> ',
      '<input id="price_from" name="price_from" type="text" value="', $priceFrom, '" />-',
      '<input name="price_to" type="text" value="', $priceTo, '" /> ',
      '<button type="submit"></button>',
      '</form>';
  }
}