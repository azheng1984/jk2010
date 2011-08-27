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
    echo '<link type="text/css" href="/css/main.css" charset="utf-8" media="screen" rel="stylesheet" />';
    echo '<script src="/js/std.js"></script>';
    echo '</head>';
  }

  private function renderBody($content) {
    echo '<body>';
    $this->renderLogo();
    echo '<div id="content">';
    $content->renderContent();
    echo '</div>';
    echo '<div id="footer">';
    echo '<div class="right">';
    foreach (explode(' ', '关于我们 广告联盟  开源项目 团队博客 联系我们') as $item) {
      echo '<a rel="nofollow" href="http://www.wj.com">'.$item.'</a> ';
    }
    echo '</div>';
    echo '<div class="left">&copy; 货比万家 <a rel="nofollow" href="http://support.wj.com/">使用条款</a> <a rel="nofollow" href="http://support.wj.com/">隐私权政策</a> 沪ICP证00000000</div>';
    echo '</div>';
    echo '</body>';
  }

  private function renderLogo() {
    echo '<div id="header">';
    echo '<div id="logo"><a href="/"><img src="/logo.png" /></a></div>';
    $this->renderSearch();
    echo '</div>';
  }

  private function renderSearch() {
    $query = isset($_GET['q']) ? $_GET['q'] : '';
    echo '<div id="search"><form action="/"><input x-webkit-speech lang="zh-CN" id="search_input" name="q" value="'.$query.'" /> <input id="search_button" type="submit" value=""/></form></div>';
  }
}