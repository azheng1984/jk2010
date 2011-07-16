<?php
abstract class Screen {
  public function render() {
    $wrapper = new ScreenWrapper;
    $wrapper->render($this);
  }

  abstract public function renderContent();
}