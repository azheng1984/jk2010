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
           echo '<div id="price"><span>价格</span>';
           self::renderPriceSequence();
           echo '</div>';
           continue;
         }
         echo '<span>', $tab, '</span>';
         continue;
       }
       echo '<a href=".', SearchUriArgument::get($tab), '" rel="nofollow">', $tab, '</a>';
     }
     echo '</div>';
     self::renderPriceRange();
     echo '<div id="total_found">找到 ', $amount, ' 个商品</div>';
  }

  private static function renderPriceSequence() {
    if (!self::$isReverse) {
      echo '<strong>低-高</strong><a href=".', SearchUriArgument::get('-价格'), '" rel="nofollow">高-低</a>';
      return;
    }
    echo '<a href=".', SearchUriArgument::get('价格'), '" rel="nofollow">低-高</a><strong>高-低</strong>';
  }

  private static function renderPriceRange() {
    $priceFrom = isset($_GET['price_from']) ? $_GET['price_from'] : '';
    $priceTo = isset($_GET['price_to']) ? $_GET['price_to'] : '';
    echo '<form id="price_range" action="."><label for="price_from">&yen;</label> ';
    if (isset($_GET['sort'])) {
      '<input name="sort" type="hidden" value="'.$_GET['sort'].'" /> ';
    }
    echo '<input id="price_from" name="price_from" type="text" value="', $priceFrom, '" />-',
      '<input name="price_to" type="text" value="', $priceTo, '" /> ',
      '<button type="submit"></button>',
      '</form>';
  }
}