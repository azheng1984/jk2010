<?php
class ApplicationBuilder {
  public function build($config) {
    $configuration = new ApplicationConfiguration;
    $handlers = $configuration->extract($config);
    $cache = new ApplicationCache($handlers);
    $directoryReader = new DirectoryReader(
      new ApplicationHandler($handlers, $cache)
    );
    $directoryReader->read($_SERVER['PWD'].DIRECTORY_SEPARATOR.'app');
    return $cache;
  }
}