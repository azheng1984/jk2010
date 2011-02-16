<?php
class ScreenWrapper {
  private $content;
  private $meta;
  private static $isGzipEnabled = false;

  public function __construct($content, $title, $meta = null) {
    $this->content = $content;
    $this->meta = $meta;
    $this->title = $title;
    $this->header = new ScreenHeader;
    $this->footer = new ScreenFooter;
  }

  public function render() {
    if (!self::$isGzipEnabled) {
      ob_start('ob_gzhandler');
      self::$isGzipEnabled = true;
    }
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
         ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n",
         '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">', "\n",
         "  <head>\n";
    if ($this->meta !== null) {
      $this->meta->render();
    }
    echo "    <title>$this->title</title>\n";
    echo '    <link type="text/css" href="/css/main.css" charset="utf-8" media="screen" rel="stylesheet" />';
    echo '  </head>', "\n",
         '  <body>', "\n";
    $this->header->render();
    echo '<div id="content">';
    $this->content->renderContent();
    echo '</div>';
    $this->footer->render();
    echo "\n", '  </body>', "\n";
    echo '</html>';
  }
}