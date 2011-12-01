<?php
class SortScreen {
  public static function render($total) {
    if (isset($_GET['sort']) && $_GET['sort'] === '价格') {
     echo '<h2>',
      '<div id="sort">排序: <a rel="nofollow" href=".">销量</a>',
      ' <span class="into">价格</span>',
      ' <a rel="nofollow" href="?sort=上架时间">上架时间</a>',
      ' <a rel="nofollow" href="?sort=折扣">折扣</a>',
      '</div>',
      '<div id="total">搜索到 ', $total, ' 个产品</div>',
      '</h2>';
      echo '<div id="option"><div><span>低-高</span>';
      echo '<a href="javascript:void(0)">高-低</a></div>';
      echo '<div class="title">范围:</div>';
      echo '<div class="start">0</div><div id="slider_wrapper"><div id="slider"></div></div><div class="end">100000</div><div class="cursor"><img id="start_img" src="/slider_active.png" /></div><div class="cursor cursor_end"><img id="end_img" src="/slider.png" /></div>';
      echo '<div><div><input id="input_start" type="text" value="" /></div> <div>-</div> <div><input id="input_end" type="text" value="" /></div></div>';
      echo '</div>';
    } else {
      echo '<h2>',
      '<div id="sort">排序: <span>销量</span>',
      ' <a rel="nofollow" href="?sort=价格">价格</a>',
      ' <a rel="nofollow" href="?sort=上架时间">上架时间</a>',
      ' <a rel="nofollow" href="?sort=折扣">折扣</a>',
      '</div>',
      '<div id="total">搜索到 ', $total, ' 个产品</div>',
      '</h2>';
    }
  }
}