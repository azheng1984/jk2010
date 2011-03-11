<?php
class ClassLoaderCacheBuilder {
  private $config;

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
    $cache = array(array(), array());
    foreach ($this->config as $key => $item) {
      $index = count($cache[1]);
      if (is_int($key)) {
        if ($item[0] !== '/') {
          $this->buildDir(null, $item, $cache);
        } else {
          $cache[1][$index] = array($item);
          $this->buildDir($index, null, $cache);
        }
      } else {
        if ($key[0] !== '/') {
          $this->buildDir(null, array($key => $item), $cache);
        } else {
          $cache[1][$index] = array($key);
          $this->buildDir($index, $item, $cache);
        }
      }
    }
    file_put_contents('cache/class_loader.cache.php', "<?php\nreturn ".var_export($cache, true).";");
  }

  private function buildDir($rootIndex, $folder, &$cache) {
    if (is_array($folder)) {
      foreach ($folder as $key => $item) {
        if (!is_int($key)) {
          foreach ($item as &$entry) {
            if ($entry === '.') {
              $entry = '';
            }
            $entry = $key.'/'.$entry;
          }
        }
        $this->buildDir($rootIndex, $item, $cache);
      }
    } else {
      $absPath = null;
      if ($folder === '.') {
        $folder = '';
      }
      if ($rootIndex === null) {
        $absPath = getcwd();
      } else {
        $absPath = $cache[1][$rootIndex][0];
      }
      if ($absPath !== '/') {
        $absPath .= '/';
      }
      $classes = array();
      $dirs = array();
      //is_dir or only current directory
      foreach (scandir($absPath.$folder) as $entry) {
        if ($entry === '..' || $entry === '.') {
          continue;
        }
        if (is_dir($absPath.$folder.'/'.$entry)) {
          if ($folder === null){
            $dirs[]= $entry;
          } else {
            $dirs[]= $folder.'/'.$entry;
          }
        } else {
          $classes[] = preg_replace('/.php$/', '', $entry);
        }
      }
      if (count($classes) !== 0) {
        $index = null;
        if ($folder === null) {
          $index = $rootIndex;
        } else {
          $index = count($cache[1]);
          if ($rootIndex === null) {
            $cache[1][$index] = $folder;
          } else {
            $cache[1][$index] = array($folder, $rootIndex);
          }
        }
        foreach ($classes as $class) {
          $cache[0][$class] = $index;
        }
      }
      if (count($dirs) !== 0) {
        $this->buildDir($rootIndex, $dirs, $cache);
      }
    }
  }
}