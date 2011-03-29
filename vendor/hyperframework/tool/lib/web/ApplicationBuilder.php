<?php
class ApplicationBuilder {
  public function build($config) {
    $configuration = new ApplicationConfiguration;
    $analyzers = $configuration->extract($config);
    $cache = new ApplicationCache($analyzers);
    $directoryReader = new DirectoryReader(
      new ApplicationAnalyzer($analyzers, $cache)
    );
    $directoryReader->read($_SERVER['PWD'].DIRECTORY_SEPARATOR.'app');
    return $cache;
  }
}