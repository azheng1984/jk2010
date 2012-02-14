<?php
class SortScreen {
  private static $orderBy = 'sale_rank';
  private static $isReverse = false;

  public static function render() {
    if (isset($_GET['sort'])) {
      self::$orderBy = $_GET['sort'];
    }
    if (self::$orderBy === '-price') {
      self::$orderBy = 'price';
      self::$isReverse = true;
    }
    self::renderTabList();
  }

  private static function renderTabList() {
    //TODO: 使用原始  mapping
    $mapping = array('销量' => 'sale_rank', '上架时间' => 'time', '折扣' => 'discount', '价格' => 'price');
    echo '<h2>排序: ';
    foreach (array('销量', '上架时间', '折扣', '价格') as $tab) {
      $value = $mapping[$tab];
      if (self::$orderBy === $value) {
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
    echo '</h2><div id="total_found">找到 ', $GLOBALS['URI']['RESULTS']['total_found'], ' 个商品</div>';//TODO:move out 
  }

  private static function renderPriceSequence() {
    if (!self::$isReverse) {
      echo '<span>低-高</span><a href=".', SearchUriArgument::get('-价格'), '" rel="nofollow">高-低</a>';
      return;
    }
    echo '<a href=".', SearchUriArgument::get('价格'), '" rel="nofollow">低-高</a><span>高-低</span>';
  }
}
