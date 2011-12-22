<?php
class CategoryScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('index_breadcrumb');
    $this->renderCssLink('category');
  }

  protected function renderBodyContent() {
    echo '<div id="breadcrumb"><span class="home"><a href="/"><img alt="首页" src="/img/home.png" /></a></span><span><a href="..">分类</a></span><h1>鞋子</h1></div>';
    echo '<div id="index">';
    $char = 65;
    echo '<h2 class="tag">属性</h2>';
    echo '<table>';
    for ($i = 0; $i < 5; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a href="品牌/">品牌</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<strong><a href="+k/">更多 &raquo;</a></strong>';
    $char = 65;
    echo '<h2>搜索</h2>';
    echo '<table>';
    for ($i = 0; $i < 5; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="/儿童+胶囊/">儿童 胶囊</a> <span>123</span></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '<strong><a href="+q/">更多 &raquo;</a></strong>';
    echo '</div>';
  }
}