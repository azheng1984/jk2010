<?php
abstract class Screen {
  abstract protected function renderHeadContent();
  abstract protected function renderBodyContent();

  protected function renderCssLink($name) {
    echo '<link type="text/css" href="/+/css/', $name, '.',
      Asset::getMd5('css/'.$name.'.css'), '.css" charset="utf-8"',
      ' media="screen" rel="stylesheet" />';
  }

  protected function renderJsLink($name) {
    echo '<script src="/+/js/', $name, '.',
      Asset::getMd5('js/'.$name.'.js'), '.js" ></script>';
  }

  public function render() {
    header('Content-Type:text/html; charset=utf-8');
    ob_start();
    echo '<!DOCTYPE html><html>';
    $this->renderHead();
    $this->renderBody();
    echo '</html>';
    $etag = md5(ob_get_contents());
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
      && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
        header('HTTP/1.1 304 Not Modified');
        ob_end_clean();
        return;
    }
    header('Etag: '.$etag);
    ob_end_flush();
  }

  private function renderHead() {
    echo '<head>';
    $this->renderCssLink('screen');
    $this->renderJsLink('jquery-1.7.1');
    $this->renderJsLink('screen');
    $this->renderHeadContent();
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
      '<a href="/+i/">网站地图</a> ',
      '</div>';
  }

  private function renderDeclaration() {
    echo '<div>&copy; 货比万家',
      ' <a href="http://help.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://help.huobiwanjia.com/privacy" rel="nofollow">隐私权政策</a>',
      '</div>';
  }
}