<?php
class HomeScreen implements IContent {
  public function render() {
    $wrapper = new ScreenWrapper($this);
    $wrapper->setTitle('hello');
    $wrapper->render();
  }

  public function renderContent() {
    
  }
}