<?php
class ClassLoaderBuilder {
  private $config;

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
    $scanner = new DirectoryScanner(new $recognizers);
    $cache = $scanner->scan($this->config);
    $writer = new CacheWriter;
    $writer->write('class_loader', $this->cache);
  }

  public function scan($folders) {
      if (is_array($this->config)) {
      foreach ($this->config as $key => $item) {
        $index = count($this->cache[1]);
        if (is_int($key)) {
          if ($item[0] !== '/') {
            $this->fetch(null, $item);
          } else {
            $this->cache[1][$index] = array($item);
            $this->fetch($index, null);
          }
        } else {
          if ($key[0] !== '/') {
            $this->fetch(null, array($key => $item));
          } else {
            $this->cache[1][$index] = array($key);
            $this->fetch($index, $item);
          }
        }
      }
    } else {
      foreach (scandir(getcwd()) as $entry) {
        if ($entry === '..' || $entry === '.') {
          continue;
        }
        $this->fetch(null, $entry);
      }
    }
    return $this->getFiles($index, $path);
  }
}