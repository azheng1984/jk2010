<?php
class HtmlMeta {
  private $metas;

  public function __construct($title, $description, $keywords) {
  	$this->addMeta('title', $title);
  	$this->addMeta('description', $description);
  	$this->addMeta('keywords', $keywords);
  }

  public function render() {
    foreach ($this->metas as $item) {
      echo '    <meta name="', $item['name'],
           '" content="', $item['content'], '" />', "\n";
    }
  }
  
  private function addMeta($name, $content) {
    $this->metas[] = array ('name' => $name, 'content' => $content);
  }
}