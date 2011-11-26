<?php
class SortScreen {
  public static function render($total) {
    echo '<h2>',
    '<div id="sort">排序: <span>销量</span>',
    ' <a rel="nofollow" href="?sort=新品">新品</a>',
    ' <a rel="nofollow" href="?sort=价格">价格</a>',
    ' <a rel="nofollow" href="?sort=折扣">折扣</a>',
    '</div>',
    '<div id="total">找到 ', $total, ' 个产品</div>',
    '</h2>';
  }
}