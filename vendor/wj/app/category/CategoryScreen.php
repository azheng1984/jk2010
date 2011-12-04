<?php
class CategoryScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('category');
  }

  protected function renderBodyContent() {
    echo '<div id="h1_wrapper"><h1><a href="/"><img src="/home.png"></a> <img src="/bread_arrow.png"> <a href="..">分类</a> <img src="/bread_arrow.png"> 鞋子</h1></div>';
    echo '<div id="list_wrapper" class="tag">';
    $char = 65;
    echo '<h2 class="tag_icon">属性</h2>';
    echo '<table>';
    for ($i = 0; $i < 6; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="品牌/">品牌</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<div class="more"><strong><a href="javascript:void(0)">更多 &raquo;</a></strong></div>';
    echo '</div>';
    echo '<div id="list_wrapper" class="search">';
    $char = 65;
    echo '<h2>搜索</h2>';
    echo '<table>';
    for ($i = 0; $i < 6; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="/儿童+胶囊/">儿童 胶囊</a> <span>123</span></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<div class="more"><strong><a href="javascript:void(0)">更多 &raquo;</a></strong></div>';
    echo '</div>';
  }
}