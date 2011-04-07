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
    $mode = $this->getMode($content);
    file_put_contents($path, $this->getOutput($content));
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

  private function getMode($content) {
    if (isset($content[0]) && is_int($content[0])) {
      return array_shift($content);
    }
  }

  private function getOutput($content) {
    if ($content !== null) {
      return implode(PHP_EOL, $content);
    }
  }
}