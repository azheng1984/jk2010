<?php
class CategoryScreen implements IContent {
  private $cache;

  public function render() {
    if ($_GET['category'] !== 'home'
     && !isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    if ($_GET['category'] === 'home') {
      $title = "甲壳";
    } else {
      $title = "{$_ENV['category'][$_GET['category']][0]}频道-甲壳";
    }
    $this->cache = require DATA_PATH."category/{$_GET['category']}.php";
    $wrapper = new ScreenWrapper($this, $title, new HtmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    $this->renderHot();
    $this->renderRencent();
  }

  private function renderDocumentListLink() {
    if (isset($this->cache['document_list_url'])) {
      echo '<a href="'.$this->cache['document_list_url'].'">更多</a>';
    }
  }

  private function renderRencent() {
    echo '<div id="recent"><span class="red_title">最新热点</span>';
    $this->renderRecentContent();
    $this->renderDocumentListLink();
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
    echo '<li><a href="', $item['url'], '">', $item['title'],
         "</a> - {$_ENV['source'][$item['source_id']]}</li>";
  }

  private function renderHot() {
    echo '<div id="hot">';
    foreach ($this->cache['hot'] as $item) {
      $this->renderHotItem($item);
    }
    $this->renderDocumentListLink();
    echo '</div>';
  }

  private function renderDocumentTitle($item) {
    echo '<div class="title"><a href="', $item['url'], '">',
         $item['title'], "</a></div>";
  }
  
  private function renderDocumentDescription($item) {
    echo '<div class="description">'.$item['description'].'</div>';
  }

  private function renderDocumentMeta($item) {
    echo '<div class="meta">';
    $outputs = array();
    foreach (array('time', 'place', 'people') as $meta) {
      if (isset($item[$meta])) {
        $outputs[] = '<span class="'.$meta.'">'.$item[$meta].'</span>';
      }
    }
    $outputs[] = '<span class="source">'
                .$_ENV['source'][$item['source_id']].'</span>';
    echo implode(' | ', $outputs);
    echo '</div>';
  }

  private function renderHotItemText($item, $hasImage) {
    echo '<div class="text';
    if ($hasImage) {
      echo ' short_text';
    }
    echo '">';
    $this->renderDocumentTitle($item);
    $this->renderDocumentDescription($item);
    $this->renderDocumentMeta($item);
    echo '</div>';
  }

  private function renderHotItem($item) {
    $hasImage = isset($item['image_url']);
    echo '<div class="item">';
    $this->renderHotItemText($item, $hasImage);
    if ($hasImage) {
      echo '<div class="image"><img src="'.$item['image_url'].'" /></div>';
    }
    echo '</div>';
  }
}