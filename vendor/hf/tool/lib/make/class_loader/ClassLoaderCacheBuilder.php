<?php
class ClassLoaderCacheBuilder {
  private $config;

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
    $cache = array(array(), array());
    foreach ($this->config as $key => $item) {
      if (is_int($item)) {
        if ($item[0] === '/') {
          $this->buildDir($item, null, $cache);
        }
      } else {
        if ($key[0] === '/') {
          $this->buildDir($key, $item, $cache);
        } else {
          $this->buildDir($key, $item, $cache, true);
        }
      }
    }
    file_put_contents('cache/class_loader.cache.php', "<?php\nreturn ".var_export($cache, true).";");
  }


  private function buildDir($root, $path, &$cache, $isRelative = false) {
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
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
        $cache[1][] = $root;
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