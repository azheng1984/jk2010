<?php
class ClassLoaderBuilder {
  public function build($config) {
    var_dump($config);
    return;
    $cache = new ClassLoaderCache;
    $directoryReader = new DirectoryReader(
      new ClassRecognizationHandler($cache)
    );
    $configuration = new ClassLoaderConfiguration;
    foreach ($configuration->extract($config) as $item) {
      $directoryReader->read($item[0], $item[1]);
    }
    return $cache;
  }
}