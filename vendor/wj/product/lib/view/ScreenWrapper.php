<?php
class ScreenWrapper {
  public function render($content) {
    header('Content-Type:text/html; charset=utf-8');
    echo '<html>';
    $this->renderHead();
    $this->renderBody($content);
    echo '</html>';
  }

  private function renderHead() {
    echo '<head>';
    echo '<title>货比万家</title>';
    echo '<script src="/js/std.js"></script>';
    echo '<style>a {color:#36C}</style>';
    echo '</head>';
  }

  private function renderBody($content) {
    echo '<body>';
    $this->renderLogo();
    $this->renderSearch();
    $content->renderContent();
    echo '<div>&copy; 2011 货比万家</div>';
    echo '</body>';
  }

  private function renderLogo() {
    echo '<div class="logo"><a href="/">货比万家</a> <span style="color:#ff6600">Lab</span></div>';
  }

  private function renderSearch() {
    $query = isset($_GET['q']) ? $_GET['q'] : '';
    echo '<div><form action="http://search.wj.com/"><input name="q" value="'.$query.'" /> <input type="submit" value="搜索"/></form></div>';
  }
}