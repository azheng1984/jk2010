<?php
class NewCommand {
  public function execute($type, $hyperframeworkPath = HYPERFRAMEWORK_PATH) {
    $this->setPath($hyperframeworkPath);
    if (count(scandir(getcwd())) !== 2) {
      throw new CommandException('directory must empty');
    }
    $config = require CONFIG_PATH.'new/'.$type.'.config.php';
    foreach ($config as $path => $content) {
      if (is_int($path)) {
        list($path, $content) = array($content, null);
      }
      if (substr($path, -1) === '/') {
        $this->generateDirectory($path, $content);
        continue;
      }
      $this->generateFile($path, $content);
    }
  }

  private function setPath($hyperframeworkPath) {
    $classLoaderPathPrefix = 'HYPERFRAMEWORK_PATH';
    if (strpos(HYPERFRAMEWORK_PATH, getcwd()) === 0) {
      $classLoaderPathPrefix = 'ROOT_PATH.'.$classLoaderPathPrefix;
      $hyperframeworkPath = str_replace(getcwd(), '', $hyperframeworkPath);
    }
    $_ENV['new'] = array('hyperframework_path' => $hyperframeworkPath);
    $_ENV['new']['class_loader_prefix'] = $classLoaderPathPrefix;
  }

  private function generateFile($path, $content) {
    $this->generateDirectory(dirname($path), null);
    $mode = 0644;
    $output = null;
    if ($content !== null) {
      $mode = array_shift($content);
      $output = implode(PHP_EOL, $content);
    }
    file_put_contents($path, $output);
    chmod($path, $mode);
  }

  private function generateDirectory($path, $mode) {
    if ($mode === null) {
      $mode = 0755;
    }
    if (!is_dir($path)) {
      $mask = umask(0);
      mkdir($path, $mode, true);
      umask($mask);
    }
  }
}