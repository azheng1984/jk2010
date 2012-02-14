<?php
class SearchSortScreen {
  public static function render() {
    echo '<h2>排序: ';
    $list = array('销量' => 'sale_rank', '上架时间' => 'time',
      '折扣' => 'discount', '价格' => 'price');
    foreach ($list as $name => $sort) {
      if ($name === '价格') {
        self::renderPriceSection();
        continue;
      }
      if ($GLOBALS['SORT'] === $sort) {
        echo '<em>', $name, '</em>';
        continue;
      }
      echo '<a href=".', SearchQueryString::get($sort),
        '" rel="nofollow">', $name, '</a>';
    }
    echo '</h2>';
  }

  private static function renderPriceSection() {
    if ($GLOBALS['SORT'] !== 'price' && $GLOBALS['SORT'] !== '-price') {
      echo '<a href=".', SearchQueryString::get('price'),
        '" rel="nofollow">价格</a>';
      return;
    }
    echo '<span id="price"><em>价格</em>';
    self::renderSortPrice();
    echo '</span>';
  }

  private static function renderSortPrice() {
    if ($GLOBALS['SORT'] === 'price') {
      echo '<span>低-高</span><a href=".', SearchQueryString::get('price'),
      '" rel="nofollow">高-低</a>';
      return;
    }
    echo '<a href=".', SearchQueryString::get('-price'),
      '" rel="nofollow">低-高</a><span>高-低</span>';
  }
}