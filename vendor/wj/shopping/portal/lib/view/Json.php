<?php
abstract class Json extends EtagView {
  abstract protected function renderJson();

  protected function renderContent() {
    header('Cache-Control: public, max-age=3600');
    header('Expires: '
      .gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME'] + 3600).' GMT');
    header('Content-Type: application/json;charset=utf-8');
    $this->renderJson();
  }
}