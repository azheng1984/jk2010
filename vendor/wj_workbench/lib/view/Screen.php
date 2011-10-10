<?php
abstract class Screen {
  abstract protected function renderHeadContent();

  abstract protected function renderBodyContent();

  public function render() {
    header('Content-Type:text/html; charset=utf-8');
    echo '<!DOCTYPE html><html>';
    $this->renderHead();
    $this->renderBody();
    echo '</html>';
  }

  private function renderHead() {
    echo '<head>';
    $this->renderHeadContent();
    echo '<link type="text/css" href="/css/screen.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
    echo '<link rel="search" type="application/opensearchdescription+xml" title="货比万家" href="/opensearch.xml" />';
    echo '</head>';
  }

  private function renderBody() {
    echo '<body>';
    $this->renderBodyWrapper();
    echo '</body>';
  }

  private function renderBodyWrapper() {
    echo '<div id="wrapper">';
    $this->renderBodyHeader();
    $this->renderBodyContent();
    $this->renderBodyFooter();
    echo '</div>';
  }

  private function renderBodyHeader() {
    echo '<div id="header">';
    $this->renderLogo();
    $this->renderSearch();
    $this->renderToolbar();
    echo '</div>';
  }

  private function renderLogo() {
    echo '<div id="logo"><a href="/">货比万家<span class="image"></span></a></div>';
  }

  private function renderSearch() {
    $query = isset($_GET['q']) ? $_GET['q'] : '';
    echo '<form id="search" action="/">',
      '<input class="text" type="text" lang="zh-CN" name="q" value="', $query, '" x-webkit-speech />',
      '<input class="submit" type="submit" value="" />',
      '</form>';
  }

  private function renderToolbar() {
    echo '<div id="toolbar">',
      '<a href="http://passport.huobiwanjia.com/sign_up" rel="nofollow">注册</a>',
      ' <a href="http://passport.huobiwanjia.com/sign_in" rel="nofollow">登录</a>',
      '</div>';
  }

  private function renderBodyFooter() {
    echo '<div id="footer">';
    $this->renderBodyFooterLinks();
    $this->renderDeclaration();
    echo '</div>';
  }

  private function renderBodyFooterLinks() {
    echo '<div class="links">',
      '<a href="http://support.huobiwanjia.com/about_us" rel="nofollow">关于我们</a> ',
      '<a href="http://union.huobiwanjia.com/" rel="nofollow">广告联盟</a> ',
      '<a href="http://code.huobiwanjia.com/" rel="nofollow">开源项目</a> ',
      '<a href="http://blog.huobiwanjia.com/" rel="nofollow">团队博客</a> ',
      '<a href="http://support.huobiwanjia.com/contact_us" rel="nofollow">联系我们</a> ',
      '</div>';
  }

  private function renderDeclaration() {
    echo '<div>&copy; 货比万家',
      ' <a href="http://support.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://support.huobiwanjia.com/privacy" rel="nofollow">隐私权政策</a>',
      '</div>';
  }
}