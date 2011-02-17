<?php
class HtmlMeta {
  private $metas = array();

  public function __construct($description = null, $keywords = null) {
    $this->addMeta('description', $description);
    $this->addMeta('keywords', $keywords);
  }

  public function render() {
    echo '    <meta http-equiv="Content-Type"', 
         ' content="text/html; charset=UTF-8"/>', "\n";
    foreach ($this->metas as $item) {
      if ($item['content'] !== null) {
        echo '    <meta name="', $item['name'],
             '" content="', $item['content'], '" />', "\n";
      }
    }
  }

  private function addMeta($name, $content) {
    if ($content != null) {
      $this->metas[] = array ('name' => $name, 'content' => $content);
    }
  }
}