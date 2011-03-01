<?php
class NewWebCommand {
  public function execute($name) {
    $config = require HF_PATH.'tool/config/scaffold/web.config.php';
    foreach ($config as $file => $content) {
      if (is_int($file)) {
        $file = $content;
        $content = null;
      }
      $this->create($file, $content);
    }
    echo 'done';
  }

  private function create($file, $content) {
    if (substr($file, -1) === '/') {
      mkdir($file, 0777, true);
    } else {
      mkdir(dirname($file), 0777, true);
      file_put_contents($file, implode("\n", $content));
    }
  }
}