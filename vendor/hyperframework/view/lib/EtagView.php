<?php
abstract class EtagView {
  abstract protected function renderContent();

  public function render() {
    ob_start();
    $this->renderContent();
    $content = ob_get_contents();
    if ($content === '') {
      return;
    }
    $etag = md5(ob_get_contents());
    if (isset($_SERVER['HTTP_IF_NONE_MATCH'])
      && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
      header('HTTP/1.1 304 Not Modified');
      ob_end_clean();
      return;
    }
    header('Etag: '.$etag);
    ob_end_flush();
  }
}
