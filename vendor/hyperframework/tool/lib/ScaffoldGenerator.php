<?php
class ScaffoldGenerator {
  public function generate($config) {
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

  private function generateFile($path, $content) {
    $this->generateDirectory(dirname($path));
    $mode = null;
    if (isset($content[0]) && is_int($content[0])) {
      $mode = array_shift($content);
    }
    $output = null;
    if ($content !== null) {
      $output = implode(PHP_EOL, $content);
    }
    file_put_contents($path, $output);
    if ($mode !== null) {
      chmod($path, $mode);
    }
  }

  private function generateDirectory($path, $mode = null) {
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