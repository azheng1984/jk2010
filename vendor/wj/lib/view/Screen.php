<?php
abstract class Screen extends EtagView {
  abstract protected function renderHtmlHeadContent();
  abstract protected function renderHtmlBodyContent();

  public function __construct() {
    header('Content-Type: text/html; charset=utf-8');
  }

  protected function renderCssLink($name) {
    echo '<link type="text/css" href="/+/css/', $name, '.',
      Asset::getMd5('css/'.$name.'.css'), '.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderJsLink($name) {
    echo '<script src="/+/js/', $name, '.',
      Asset::getMd5('js/'.$name.'.js'), '.js" ></script>';
  }

  public function renderBody() {
    echo '<!DOCTYPE html><html>';
    $this->renderHtmlHead();
    $this->renderHtmlBody();
    echo '</html>';
  }

  private function renderHtmlHead() {
    echo '<head>';
    $this->renderCssLink('screen');
    $this->renderJsLink('jquery-1.7.1');
    $this->renderJsLink('screen');
    $this->renderHtmlHeadContent();
    echo '</head>';
  }

  private function renderHtmlBody() {
    echo '<body>';
    $this->renderBodyWrapper();
    echo '</body>';
  }

  private function renderBodyWrapper() {
    echo '<div id="wrapper">';
    $this->renderBodyHeader();
    $this->renderHtmlBodyContent();
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
    echo '<div id="logo">',
      '<a href="/">货比万家<span></span></a></div>';
  }

  private function renderSearch() {
    $query = isset($GLOBALS['URI']['QUERY']) ? $GLOBALS['URI']['QUERY'] : '';
    echo '<form action="/">',
      '<input type="text" name="q" value="',
      htmlentities($query, ENT_QUOTES, 'utf-8'), '" autocomplete="off" />',
      '<button type="submit"></button></form>';
  }

  private function renderToolbar() {
    echo '<div id="toolbar">',
      '<a href="http://passport.huobiwanjia.com/sign_up" rel="nofollow">',
      '注册</a>',
      ' <a href="http://passport.huobiwanjia.com/sign_in" rel="nofollow">',
      '登录</a></div>';
  }

  private function renderBodyFooter() {
    echo '<div id="footer">';
    $this->renderBodyFooterLinkList();
    $this->renderDeclaration();
    echo '</div>';
  }

  private function renderBodyFooterLinkList() {
    echo '<div class="link">',
      '<a href="http://help.huobiwanjia.com/about_us" rel="nofollow">关于我们</a> ',
      '<a href="http://ad.huobiwanjia.com/" rel="nofollow">广告工具</a> ',
      '<a href="http://code.huobiwanjia.com/" rel="nofollow">开源项目</a> ',
      '<a href="http://blog.huobiwanjia.com/" rel="nofollow">团队博客</a> ',
      '<a href="http://help.huobiwanjia.com/contact_us" rel="nofollow">联系我们</a> ',
      '<a href="/+i/">分类索引</a> ',
      '</div>';
  }

  private function renderDeclaration() {
    echo '<div>&copy; 货比万家',
      ' <a href="http://help.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://help.huobiwanjia.com/privacy" rel="nofollow">隐私权政策</a>',
      '</div>';
  }
}