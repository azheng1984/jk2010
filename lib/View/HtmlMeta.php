<?php
class HtmlMeta {
  private $metas;

  public function __construct($description, $keywords) {
    $this->addMeta('description', $description);
    $this->addMeta('keywords', $keywords);
  }

  public function render() {
    echo '    <meta http-equiv="Content-Type"', 
         ' content="text/html; charset=UTF-8"/>', "\n";
    foreach ($this->metas as $item) {
      echo '    <meta name="', $item['name'],
           '" content="', $item['content'], '" />', "\n";
    }
  }

  private function addMeta($name, $content) {
    $this->metas[] = array ('name' => $name, 'content' => $content);
  }
}