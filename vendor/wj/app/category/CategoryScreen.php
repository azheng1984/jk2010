<?php
class CategoryScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('category');
  }

  protected function renderBodyContent() {
    echo '<div id="h1_wrapper"><h1><img src="/home.png"> <img src="/bread_arrow.png"> <a href="..">分类</a> <img src="/bread_arrow.png"> 鞋子</h1></div>';
    echo '<div id="list_wrapper">';
    $char = 65;
    echo '<h2><img src="/tag.png">属性</h2>';
    echo '<table>';
    for ($i = 0; $i < 6; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="鞋子/">鞋子</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<a href="javascript:void(0)">更多 &raquo;</a>';
    echo '</div>';
    echo '<div id="list_wrapper">';
    $char = 65;
    echo '<h2>搜索</h2>';
    echo '<table>';
    for ($i = 0; $i < 6; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="鞋子/">鞋子</a><span>123</span></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<a href="javascript:void(0)">更多 &raquo;</a>';
    echo '</div>';
  }
}