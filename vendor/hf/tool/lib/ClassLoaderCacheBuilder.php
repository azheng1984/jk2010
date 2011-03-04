<?php
class ClassLoaderCacheBuilder {
  public function buildClassLoaderCache() {
    $cache = array(array(), array(), array());
    foreach ($this->config['class_loader'] as $key => $item) {
      if (!is_array($item)) {
        if (is_int($key)) {
          $key = null;
        }
        $key = $key.$item;
        $item = array('');
      }
      $root = realpath($key);
      foreach ($item as $path) {
        $this->buildDir($root, $path, $cache);
      }
    }
    if (count($cache[2]) === 0) {
      $cache[2] = null;
    }
    file_put_contents('cache/class_loader.cache.php', "<?php\nreturn ".var_export($cache, true).";");
  }

  private function buildDir($root, $path, &$cache) {
    $dirPath = $root.'/'.$path;
    $dir = dir($dirPath);
    $classes = array();
    $dirs = array();
    foreach (scandir($dirPath) as $entry) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $dirs[]= $entry;
      } else {
        $classes[] = preg_replace('/.php$/', '', $entry);
      }
    }
    if (count($classes) !== 0) {
      $rootIndex = null;
      if (($rootIndex = array_search($root, $cache[2], true)) !== false) {
      } else if ($root !== realpath('.')) {
        $rootIndex = count($cache[2]);
        $cache[2][] = $root;
      }
      $index = count($cache[1]);
      if ($rootIndex === false) {
        $cache[1][$index] = $path;
      } else {
        $cache[1][$index] = array($path => $rootIndex);
      }
      foreach ($classes as $class) {
        $cache[0][$class] = $index;
      }
    }
    foreach ($dirs as $entry) {
      $this->buildDir($root, $path.'/'.$entry, $cache);
    }
  }
}