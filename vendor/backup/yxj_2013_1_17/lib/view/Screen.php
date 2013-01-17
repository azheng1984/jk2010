<?php
abstract class Screen extends EtagView {
  private $jsList = array();
  private $cssList = array();
  private $js = '';
  private $stop = false;

  abstract protected function renderHtmlHeadContent();
  abstract protected function renderHtmlBodyContent();

  public function addJs($js) {
    $this->js .= $js;
  }

  protected function addCssLink($name) {
    $this->cssList[] = $name;
  }

  protected function addJsLink($name) {
    $this->jsList[] = $name;
  }

  protected function stop() {
    $stop = true;
  }

  protected function renderContent() {
    header('Cache-Control: private, max-age=0');
    if ($this->stop === false) {
      echo '<!DOCTYPE html><html>';
      $this->renderHtmlHead();
      $this->renderHtmlBody();
      echo '</html>';
    }
  }

  private function renderCssLinkList() {
    foreach ($this->cssList as $name) {
      echo '<link type="text/css" href="/asset/css/', $name, '.',
        Asset::getMd5('css/'.$name.'.css'), '.css"',
        ' media="screen" rel="stylesheet"/>';
    }
  }

  private function renderJsLinkList() {
    foreach ($this->jsList as $name) {
      echo '<script type="text/javascript" src="/asset/js/', $name, '.',
        Asset::getMd5('js/'.$name.'.js'), '.js"></script>';
    }
  }

  private function renderHtmlHead() {
    echo '<head><meta charset="UTF-8"/>';
//     $this->addCssLink('screen');
//     $this->addJsLink('jquery-1.7.2');
//     $this->addJsLink('screen');
    $this->renderHtmlHeadContent();
    $this->renderCssLinkList();
    echo '</head>';
  }

  private function renderHtmlBody() {
    echo '<body>';
    $this->renderBodyWrapper();
    $this->renderJsLinkList();
    $this->renderJs();
    echo '</body>';
  }

  private function renderBodyWrapper() {
    //echo '<div id="wrapper">';
    $this->renderBodyHeader();
    $this->renderHtmlBodyContent();
    $this->renderBodyFooter();
    //echo '</div>';
  }

  private function renderBodyHeader() {
    echo '<div id="header">';
    $this->renderLogo();
    $this->renderSearch();
    echo '</div>';
  }

  private function renderLogo() {
    echo '<div id="logo"><a href="/">优选集<span></span></a></div>';
    setcookie('publisher', 'test');
  }

  private function renderSearch() {
    $query = isset($GLOBALS['QUERY']) ?
      htmlentities($GLOBALS['QUERY']['name'], ENT_QUOTES, 'UTF-8') : '';
    echo '<form action="/"><input type="text" name="q" maxlength="100" value="',
      $query, '"/> <button type="submit">搜索</button></form>';
  }

  private function renderBodyFooter() {
    echo '<div id="footer"><div class="content">';
    $this->renderDeclaration();
    echo '</div></div>';
  }

  private function renderDeclaration() {
    echo '© 2012 优选集 ',
      '<a href="http://', $GLOBALS['DOMAIN_PREFIX'],
       'youxuanji.com/book/1/">关于优选集</a> ',
      '<a href="http://', $GLOBALS['DOMAIN_PREFIX'],
      'about.huobiwanjia.com/terms_of_use" rel="nofollow">使用条款</a>',
      ' <a href="http://', $GLOBALS['DOMAIN_PREFIX'],
      'about.huobiwanjia.com/privacy"  rel="nofollow">隐私权政策</a> 沪ICP备0000000000号';
  }

  private function renderJs() {
    if ($this->js !== '') {
      echo '<script type="text/javascript">', $this->js, '</script>';
    }
  }
}