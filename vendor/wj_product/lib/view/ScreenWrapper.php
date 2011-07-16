<?php
class ScreenWrapper {
  private $content;
  private $meta;

  public function __construct($content, $title, $meta = null) {
    $this->content = $content;
    $this->title = $title;
    $this->meta = $meta === null ? new HtmlMeta : $meta;
    $this->header = new ScreenHeader;
    $this->footer = new ScreenFooter;
  }

  public function render() {
    if (extension_loaded('zlib')) {
      ini_set('zlib.output_compression', 'On');
    }
    header('Content-Type:text/html; charset=utf-8');
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
         ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
         '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">',
         "<head>";
    $this->meta->render();
    echo "<title>$this->title</title>";
    echo '<link type="text/css" href="/css/main.css" charset="utf-8" media="screen" rel="stylesheet" />';
    echo '</head><body>';
    $this->header->render();
    echo '<div id="content">';
    $this->content->renderContent();
    echo '</div>';
    $this->footer->render();
    echo '</body></html>';
  }
}