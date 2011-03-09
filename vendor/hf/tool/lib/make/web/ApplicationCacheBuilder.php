<?php
class ApplicationCacheBuilder {
  private $config;

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
    $cache = array();
    $this->buildApp('', $cache);
    file_put_contents('cache/application.cache.php', "<?php\nreturn ".var_export($cache, true).';');
  }

  private function buildApp($path, &$cache) {
    $pathCache = array();
    $dirs = array();
    $dirPath = getcwd().'/app/'.$path;
    $actionProcessorCacheBuilder = new ActionProcessorCacheBuilder;
    $viewProcessorCacheBuilder = new ViewProcessorCacheBuilder;
    foreach (scandir($dirPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $dirs[]= $entry;
        continue;
      }
      $actionProcessorCacheBuilder->build($dirPath, $entry, $pathCache);
      $viewProcessorCacheBuilder->build($entry, $pathCache);
    }
    if (count($pathCache) !== 0) {
      $cache[$path] = $pathCache;
    }
    foreach ($dirs as $entry) {
      $this->buildApp($path.'/'.$entry, $cache);
    }
  }
}