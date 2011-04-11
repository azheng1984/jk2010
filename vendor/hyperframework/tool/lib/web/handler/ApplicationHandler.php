<?php
class ApplicationHandler {
  private $handler;
  private $cache;

  public function __construct($handlers, $cache) {
    $this->handler = $handlers;
    $this->cache = $cache;
  }

  public function handle($fullPath, $relativeFolder, $rootFolder) {
    foreach ($this->handler as $name => $handler) {
      $cache = $handler->execute(basename($fullPath), $fullPath);
      if ($cache !== null) {
        $this->cache->append(
          DIRECTORY_SEPARATOR.$relativeFolder, $name, $cache
        );
        return;
      }
    }
  }
}