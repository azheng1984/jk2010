<?php
class HomeScreen implements IContent {
  public function render() {
    $htmlMeta = new HtmlMeta('homt description', 'home keywords');
    $wrapper = new ScreenWrapper($this, 'home title', $htmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    echo 'hi';
  }
}