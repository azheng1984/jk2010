<?php
class ScreenWrapper {
  public function render($content) {
    echo '<html>';
    $this->renderHead();
    $this->renderBody($content);
    echo '</html>';
  }

  private function renderHead() {
    echo '<head>';
    echo '<title>货比万家</title>';
    echo '<script src="/js/std.js"></script>';
    echo '</head>';
  }

  private function renderBody($content) {
    echo '<body>';
    $this->renderLogo();
    $this->renderSearch();
    $this->renderToolbar();
    $content->renderContent();
    $this->renderFooterLeft();
    $this->renderFooterRight();
    echo '<div>&copy; 2011 货比万家</div>';
    echo '</body>';
  }

  private function renderLogo() {
    echo '<div><a href="/">货比万家</a> lab</div>';
  }

  private function renderSearch() {
    $query = isset($_GET['q']) ? $_GET['q'] : '';
    echo '<div><form action="/search"><input name="q" value="'.$query.'" /> <input type="submit" value="搜索"/></form></div>';
  }

  private function renderToolbar() {
    echo '<ul><li><a rel="nofollow" href="http://contributor.wj.com/login">登录</a></li><li>注册</li></ul>';
  }

  private function renderFooterLeft() {
    echo '<style>#foot-link a {color:#999999; font-size:12px;}</style><div id="foot-link"><a href="http://project.wj.com/">开源项目</a> | <a href="http://blog.wj.com/">团队博客</a> | <a href="/">使用条款</a> | <a href="/">隐私权政策</a> | <a href="http://support.wj.com/">联系我们</a></div>';
  }

  private function renderFooterRight() {
    echo '<div>[ <a href="http://publisher.wj.com">广告网络</a> | <a href="http://advertiser.wj.com/">商家联盟</a> | <a href="http://developer.wj.com/">开放平台</a> ]</div>';
  }
}