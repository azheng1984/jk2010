<?php
class FileSystemGenerator {
  public function generateFile($path, $content) {
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

  public function generateDirectory($path, $mode = null) {
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