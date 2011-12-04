<?php
class CategoryListScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('category_list');
  }

  protected function renderBodyContent() {
    echo '<div id="h1_wrapper"><h1><a href="/"><img src="/home.png"></a> <img src="/bread_arrow.png"> 分类</h1></div>';
        echo '<div id="list_wrapper">';
    $char = 65;
    echo '<div id="alphabet">索引: ';
    for ($i = 0; $i < 24; ++$i) {
      echo '<a href="javascript:void(0)">'.chr($char + $i).'</a>';
    }
    echo '<a href="javascript:void(0)">0-9</a>';
    echo '</div>';
    echo '<table>';
    for ($i = 0; $i < 10; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a class="categoty" href="鞋子/">鞋子</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
    echo '</div>';
    echo '<div id="pagination"><a href="javascript:void(0)">1</a></div>';
  }
}