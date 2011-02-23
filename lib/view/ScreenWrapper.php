<?php
class ScreenWrapper {
  private $content;
  private $meta;
  private static $isFirst = true;

  public function __construct($content, $title, $meta = null) {
    $this->content = $content;
    $this->title = $title;
    $this->meta = $meta === null ? new HtmlMeta : $meta;
    $this->header = new ScreenHeader;
    $this->footer = new ScreenFooter;
  }

  public function render() {
    if (self::$isFirst) {
      header('Content-Type:text/html; charset=utf-8');
      self::$isFirst = false;
    }
    ob_start('ob_gzhandler');
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
         ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n",
         '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">', "\n",
         "  <head>\n";
    $this->meta->render();
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