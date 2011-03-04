<?php
class ApplicationCacheBuilder {
  public function buildApplicationCache() {
    $cache = array();
    $this->buildApp('', $cache);
    file_put_contents('cache/application.cache.php', "<?php\nreturn ".var_export($cache, true).';');
  }

  private function buildApp($path, &$cache) {
    $pathCache = array();
    $dirs = array();
    $dirPath = getcwd().'/app/'.$path;
    foreach (scandir($dirPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $dirs[]= $entry;
        continue;
      }
    }
    if (count($pathCache) !== 0) {
      $cache[$path] = $pathCache;
    }
    foreach ($dirs as $entry) {
      $this->buildApp($path.'/'.$entry, $cache);
    }
  }
}