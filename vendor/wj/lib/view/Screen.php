<?php
abstract class Screen extends EtagView {
  private $jsList = array();
  private $cssList = array();
  private $js = '';

  abstract protected function renderHtmlHeadContent();
  abstract protected function renderHtmlBodyContent();

  protected function renderContent() {
    header('Cache-Control: private, max-age=0');
    echo '<!DOCTYPE html><html>';
    $this->renderHtmlHead();
    $this->renderHtmlBody();
    echo '</html>';
  }

  protected function addCssLink($name) {
    $this->cssList[] = $name;
  }

  protected function addJsLink($name) {
    $this->jsList[] = $name;
  }

  protected function addJs($js) {
    $this->js .= $js;
  }

  private function renderCssLinkList() {
    foreach ($this->cssList as $name) {
      echo '<link type="text/css" href="/+/css/', $name, '.',
        Asset::getMd5('css/'.$name.'.css'), '.css"',
        ' media="screen" rel="stylesheet"/>';
    }
  }

  private function renderJsLinkList() {
    foreach ($this->jsList as $name) {
      echo '<script type="text/javascript" src="/+/js/', $name, '.',
        Asset::getMd5('js/'.$name.'.js'), '.js"></script>';
    }
  }

  private function renderHtmlHead() {
    echo '<head>';
    $this->addCssLink('screen');
    $this->addJsLink('jquery-1.7.1');
    $this->addJsLink('screen');
    $this->renderHtmlHeadContent();
    $this->renderCssLinkList();
    $this->renderJsLinkList();
    echo '</head>';
  }

  private function renderHtmlBody() {
    echo '<body>';
    $this->renderBodyWrapper();
    $this->renderJs();
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
    echo '<div id="banner"><span>100%公司经营</span>',
      ' <span class="left">100%正规商店</span></div>';
  }

  private function renderLogo() {
    echo '<div id="logo"><a href="/">货比万家<span></span></a></div>';
  }

  private function renderSearch() {
    $query = isset($GLOBALS['QUERY']) ? $GLOBALS['QUERY'] : '';
    echo '<form action="/"><input type="text" name="q" value="',
      htmlentities($query, ENT_QUOTES, 'UTF-8'), '" autocomplete="off"/>',
      '<button type="submit"></button></form>';
  }

  private function renderBodyFooter() {
    echo '<div id="footer">';
    $this->renderBodyFooterDiv();
    $this->renderDeclaration();
    echo '</div>';
  }

  private function renderBodyFooterDiv() {
    echo '<div>',
      '<a href="http://i.huobiwanjia.com/" rel="nofollow">关于货比万家</a> ',
      '<a href="http://ad.huobiwanjia.com/" rel="nofollow">广告</a> ',
      '<a href="/+i/">分类索引</a> ';
    $this->renderPublisher();
    echo '</div>';
  }

  private function renderPublisher() {
    if (isset($_COOKIE['publisher'])) {//TODO
      echo '合作伙伴:<a href="/"></a>';
    }
  }

  private function renderDeclaration() {
    echo '&copy; 货比万家',
      ' <a href="http://i.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://i.huobiwanjia.com/privacy" rel="nofollow">隐私权政策</a>';
  }

  private function renderJs() {
    if ($this->js !== '') {
      echo '<script type="text/javascript">', $this->js, '</script>';
    }
  }
}