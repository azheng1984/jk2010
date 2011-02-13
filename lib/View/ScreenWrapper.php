<?php
class ScreenWrapper {
  private $content;
  private $meta;

  public function __construct($content, $title, $meta) {
    $this->content = $content;
    $this->meta = $meta;
    $this->title = $title;
    $this->header = new ScreenHeader;
    $this->footer = new ScreenFooter;
  }

  public function render() {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"',
         ' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n",
         '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN">', "\n",
         "  <head>\n";
    $this->meta->render();
    echo "    <title>$this->title</title>\n";
    echo '  </head>', "\n",
         '  <body>', "\n";
    $this->header->render();
    $this->content->renderContent();
    $this->footer->render();
    echo "\n", '  </body>', "\n";
    echo '</html>';
  }
}