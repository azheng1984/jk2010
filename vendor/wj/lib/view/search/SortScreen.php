<?php
class SortScreen {
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
    if (self::$orderBy === '价格') {
      self::renderPriceOption();
    }
  }

  private static function renderTabList($amount) {
     echo '<h2><div id="sort"';
     if (self::$orderBy === '价格') {
       echo ' class="order_by_price" ';
     }
     echo '>排序: ';
     foreach (array('销量', '价格', '上架时间', '折扣') as $tab) {
       if (self::$orderBy === $tab) {
         echo '<span>', $tab, '</span>';
         continue;
       }
       if ($tab === '销量') {
         echo '<a rel="nofollow" href=".">销量</a>';
         continue;
       }
       echo '<a rel="nofollow" href="?sort=', $tab, '">', $tab, '</a>';
     }
     echo '</div><div id="amount">找到 ', $amount, ' 个产品</div></h2>';
  }

  private static function renderPriceOption() {
    echo '<div id="option">';
    self::renderPriceOrder();
    self::renderPriceLimit();
    echo '</div>';
  }

  private static function renderPriceOrder() {
    if (!self::$isReverse) {
      echo '<strong>低-高</strong><a rel="nofollow" href="?sort=-价格">高-低</a>';
      return;
    }
    echo '<a rel="nofollow" href="?sort=价格">低-高</a><strong>高-低</strong>';
  }

  private static function renderPriceLimit() {
    echo '<div class="limit"><label for="lowest_price">范围:</label> ',
      '<input id="price_from" type="text" value="" />-',
      '<input id="price_to" type="text" value="" />',
      ' <a href="javascript:void(0)">确定</a></div>';
  }
}