<?php
class HomeScreen implements IContent {
  public function render() {
    $htmlMeta = new HtmlMeta('title', 'description', 'keywords');
    $wrapper = new ScreenWrapper($this, $htmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    
  }
}