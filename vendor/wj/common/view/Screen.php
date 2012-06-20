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
    if ($this->stop === false) {
      echo '<!DOCTYPE html><html>';
      $this->renderHtmlHead();
      $this->renderHtmlBody();
      echo '</html>';
    }
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
    echo '<head><meta charset="UTF-8"/>';
    $this->renderHtmlHeadContent();
    $this->renderCssLinkList();
    echo '</head>';
  }

  private function renderHtmlBody() {
    echo '<body>';
    $this->renderHtmlBodyContent();
    $this->renderJsLinkList();
    $this->renderJs();
    echo '</body>';
  }

  private function renderJs() {
    if ($this->js !== '') {
      echo '<script type="text/javascript">', $this->js, '</script>';
    }
  }
}
