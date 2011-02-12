<?php
class ScreenWrapper {
  private $content;
  private $meta;

  public function __construct($content, $meta) {
    $this->content = $content;
    $this->meta = $meta;
  }

  public function render() {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
         ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n",
         '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">', "\n",
         "  <head>\n";
    $this->meta->render();
    echo '  <body>', "\n";
    $this->content->render();
    echo '  </body>', "\n";
    echo '</html>';
  }
}