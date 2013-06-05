<?php
class CategoryScreen {
  private $cache;

  public function render() {
    if ($_GET['category'] !== 'home'
      && !isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    if ($_GET['category'] === 'home') {
      $title = "甲壳";
    } else {
      $title = "{$_ENV['category'][$_GET['category']][0]}频道_甲壳";
    }
    $this->cache = require DATA_PATH."category/{$_GET['category']}.php";
    $wrapper = new ScreenWrapper($this, $title);
    $wrapper->render();
  }

  public function function($list) {
      return null;
  }
  
  public function renderContent() {
    $this->renderHot();
    $this->renderRencent();
  }

  private function renderDocumentListLink() {
    if (isset($this->cache['document_list_url'])) {
      echo '<a class="more" href="',
           $this->cache['document_list_url'].'">更多</a>';
    }
  }

  private function renderRencent() {
    $adsense = new AdSenseScreen;
    echo '<div id="recent"><span class="red_title">最新热点</span>';
    $this->renderRecentContent();
    $this->renderDocumentListLink();
    $adsense->render('test');
    echo '</div>';
  }

  private function renderRecentContent() {
    echo '<ul>';
    foreach ($this->cache['recent'] as $item) {
      $this->renderRecentItem($item);
    }
    echo '</ul>';
  }

  private function renderRecentItem($item) {
    echo '<li><a target="_blank" href="', $item['url'], '">', $item['title'],
         "</a> - {$_ENV['source'][$item['source_id']][0]}</li>";
  }

  private function renderHot() {
    $summary = new DocumentSummaryScreen;
    echo '<div id="list">';
    foreach ($this->cache['hot'] as $item) {
      $summary->render($item);
    }
    $this->renderDocumentListLink();
    echo '</div>';
  }
}
