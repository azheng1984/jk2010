<?php
class ScaffoldGenerator {
  public function generate($type) {
    if (count(scandir(getcwd())) !== 2) {
      throw new CommandException('directory must empty');
    }
    $config = require CONFIG_PATH.'scaffold_generator/'.$type.'.config.php';
    foreach ($config as $path => $content) {
      if (is_int($path)) {
        $path = $content;
        $content = null;
      }
      if (substr($path, -1) === '/') {
        $this->createDirectory($path);
        continue;
      }
      $this->createFile($path, $content);
    }
  }

  private function createFile($path, $content) {
    $this->createDirectory(dirname($path));
    $data = null;
    if (is_array($content)) {
      $data = implode(PHP_EOL, $content);
    }
    file_put_contents($path, $data);
  }

  private function createDirectory($path) {
    if (!is_dir($path)) {
      mkdir($path, 0777, true); //todo:code dirs are readable only
    }
  }
}