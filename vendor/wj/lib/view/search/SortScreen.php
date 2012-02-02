<?php
class SortScreen {
  private static $orderBy = '销量';
  private static $isReverse = false;

  public static function render() {
    if (isset($_GET['sort'])) {
      self::$orderBy = $_GET['sort'];
    }
    if (self::$orderBy === '-价格') {
      self::$orderBy = '价格';
      self::$isReverse = true;
    }
    self::renderTabList();
  }

  private static function renderTabList() {
     echo '<h2>排序: ';
     foreach (array('销量', '上架时间', '折扣', '价格') as $tab) {
       if (self::$orderBy === $tab) {
         if ($tab === '价格') {
           echo '<span id="price"><em>价格</em>';
           self::renderPriceSequence();
           echo '</span>';
           continue;
         }
         echo '<em>', $tab, '</em>';
         continue;
       }
       echo '<a href=".', SearchUriArgument::get($tab), '" rel="nofollow">', $tab, '</a>';
     }
     echo '</h2><div id="total_found">找到 ', $GLOBALS['URI']['RESULTS']['total_found'], ' 个商品</div>';
  }

  private static function renderPriceSequence() {
    if (!self::$isReverse) {
      echo '<span>低-高</span><a href=".', SearchUriArgument::get('-价格'), '" rel="nofollow">高-低</a>';
      return;
    }
    echo '<a href=".', SearchUriArgument::get('价格'), '" rel="nofollow">低-高</a><span>高-低</span>';
  }
}