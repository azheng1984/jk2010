<?php
abstract class Screen extends EtagView {
  abstract protected function renderHtmlHeadContent();
  abstract protected function renderHtmlBodyContent();

  protected function renderCssLink($name) {
    echo '<link type="text/css" href="/+/css/', $name, '.',
      Asset::getMd5('css/'.$name.'.css'), '.css"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderJsLink($name) {
    echo '<script type="text/javascript" src="/+/js/', $name, '.',
      Asset::getMd5('js/'.$name.'.js'), '.js"></script>';
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
    $this->renderBanner();
    echo '</div>';
  }

  private function renderBanner() {
    echo '<div id="banner"><span>100%公司经营</span><span class="left">100%正规商店</span></div>';
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

  private function renderBodyFooter() {
    echo '<div id="footer">';
    $this->renderBodyFooterLinkList();
    $this->renderDeclaration();
    $this->renderPublisher();
    echo '</div>';
  }

  private function renderBodyFooterLinkList() {
    echo '<div>',
      '<a href="http://help.huobiwanjia.com/about_us" rel="nofollow">关于我们</a> ',
      '<a href="http://ad.huobiwanjia.com/" rel="nofollow">广告工具</a> ',
      '<a href="http://code.huobiwanjia.com/" rel="nofollow">开源项目</a> ',
      '<a href="http://blog.huobiwanjia.com/" rel="nofollow">团队博客</a> ',
      '<a href="http://help.huobiwanjia.com/contact_us" rel="nofollow">联系我们</a> ',
      '<a href="/+i/">分类索引</a> ',
      '</div>';
  }

  private function renderDeclaration() {
    echo '&copy; 货比万家',
      ' <a href="http://help.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://help.huobiwanjia.com/privacy" rel="nofollow">隐私权政策</a>';
  }

  private function renderPublisher() {
    echo '<div id="publisher">合作伙伴:<a href="#">太平洋数码</a></div>';
  }
}