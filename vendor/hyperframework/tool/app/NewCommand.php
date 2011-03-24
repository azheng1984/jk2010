<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $_ENV['hyperframework_path'] = $hyperframeworkPath;
    if (count(scandir(getcwd())) !== 2) {
      throw new CommandException('directory must empty');
    }
    $config = require CONFIG_PATH.'new/'.$type.'.config.php';
    foreach ($config as $path => $content) {
      if (is_int($path)) {
        $path = $content;
        $content = null;
      }
      if (substr($path, -1) === '/') {
        $this->generateDirectory($path);
        continue;
      }
      $this->generateFile($path, $content);
    }
  }

  private function generateFile($path, $content) {
    $this->createDirectory(dirname($path));
    $data = null;
    if (is_array($content)) {
      $data = implode(PHP_EOL, $content);
    }
    file_put_contents($path, $data);
  }

  private function generateDirectory($path) {
    if (!is_dir($path)) {
      mkdir($path, 0777, true); //todo:code dirs are readable only for php
    }
  }
}