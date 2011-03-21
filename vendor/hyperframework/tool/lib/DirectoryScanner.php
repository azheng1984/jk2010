<?php
class DirectoryScanner {
  private $classRecognizer;

  public function __construct($callback) {
    call_user_func_array($callback, array($fileName, $fullPath));
  }

  public function scan($path) {
    $fullPath = $path;
    if (DIRECTORY_SEPARATOR !== '/') {
      $fullPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
    $fullPath = (
      getcwd().DIRECTORY_SEPARATOR.'app'.$fullPath.DIRECTORY_SEPARATOR
    );
    $dirs = array();
    foreach (scandir($fullPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($fullPath.$entry)) {
        $dirs[] = $entry;
        continue;
      }
      $this->dispatch(
        $fullPath.DIRECTORY_SEPARATOR.$entry, $path, $entry
      );
    }
    foreach ($dirs as $entry) {
      $this->scan($path.'/'.$entry);
    }
  }
}