<?php
class TestScreen {
  public function render() {
    $_ENV['callback'] = 'TestScreen.render';
  }
}