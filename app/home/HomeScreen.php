<?php
class HomeScreen implements IContent {
  public function render() {
    $htmlMeta = new HtmlMeta('home title', 'homt description', 'home keywords');
    $wrapper = new ScreenWrapper($this, $htmlMeta);
    $wrapper->render();
  }

  public function renderContent() {
    echo 'hi';
  }
}