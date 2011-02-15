<?php
class DocumentListScreen implements IContent {
  public function render() {
    print_r($_GET);
  }

  public function renderContent() {
    echo 'hi';
  }
}