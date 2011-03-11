<?php
class ScaffoldGenerator {
  public function generate($type) {
    $config = require HF_PATH.'tool/config/scaffold/'.$type.'.config.php';
    if (count(scandir(getcwd())) !== 2) {
      throw new CommandException('directory must empty'."\n");
    }
    foreach ($config as $file => $content) {
      if (is_int($file)) {
        $file = $content;
        $content = null;
      }
      $this->create($file, $content);
    }
  }

  private function create($file, $content) {
    if (substr($file, -1) === '/') {
      if (!is_dir($file)) {
        mkdir($file, 0777, true);
      }
    } else {
      if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0777, true);
      }
      $data = '';
      if (is_array($content)) {
        $data = implode("\n", $content);
      }
      file_put_contents($file, $data);
    }
  }
}