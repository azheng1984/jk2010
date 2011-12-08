<?php
class IndexScreen extends Screen {
  protected function renderHeadContent() {
    echo '<title>货比万家</title>';
    $this->renderCssLink('index_breadcrumb');
    $this->renderCssLink('index');
  }

  protected function renderBodyContent() {
    $this->renderBreadcrumb();
    $this->renderIndex();
  }

  private function renderIndex() {
    echo '<div id="index">';
    $this->renderAlphabet();
    $this->renderTable();
    $this->renderPagination();
    echo '</div>';
  }

  private function renderBreadcrumb() {
    echo '<div id="breadcrumb">',
      '<span class="home"><a href="/"><img alt="首页" src="/img/home.png" /></a></span><h1>分类</h1>',
      '</div>';
  }

  private function renderAlphabet() {
    $char = 65;
    echo '<div id="alphabet">索引: ';
    for ($i = 0; $i < 24; ++$i) {
      echo ' <a href="javascript:void(0)">'.chr($char + $i).'</a>';
    }
    echo ' <a href="javascript:void(0)">0-9</a>';
    echo '</div>';
  }

  private function renderTable() {
    echo '<table>';
    for ($i = 0; $i < 10; ++$i) {
      echo '<tr>';
      for ($j = 0; $j < 5; ++$j) {
        echo '<td><a href="鞋子/">鞋子</a></td>';
      }
      echo '</tr>';
    }
    echo '</table>';
  }

  private function renderPagination() {
    echo '<div id="pagination"><span>1</span> <a href="javascript:void(0)">2</a> <a href="javascript:void(0)">3</a></div>';
  }
}