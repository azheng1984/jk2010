<?php
class ClassLoaderCacheBuilder {
  private $config;
  private $cache = array(array(), array());

  public function __construct($config) {
    $this->config = $config;
  }

  public function build() {
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
    if (count($this->cache[1]) === 0) {
      unset($this->cache[1]);
    }
    $writer = new CacheWriter;
    $writer->write('class_loader', $this->cache);
  }

  private function fetch($rootIndex, $folder) {
    if (is_array($folder)) {
      foreach ($folder as $key => $item) {
        if (!is_int($key)) {
          foreach ($item as &$entry) {
            $entry = $key.'/'.$entry;
          }
        }
        $this->fetch($rootIndex, $item);
      }
    } else {
      $absPath = null;
      if ($rootIndex === null) {
        $absPath = getcwd();
      } else {
        $absPath = $this->cache[1][$rootIndex][0];
      }
      if ($absPath !== '/') {
        $absPath .= '/';
      }
      //todo:use is array to set is recursive, not '.'
      $isRecursive = basename($folder) !== '.';
      if (!$isRecursive) {
        $folder = preg_replace('/\/.$/', '', $folder);
      }
      if ($folder !== null && $folder !== '.') {
        $absPath = $absPath.$folder.DIRECTORY_SEPARATOR;
      }
      $classes = array();
      $dirs = array();
      $files = array();
      if (is_dir($absPath)) {
        foreach (scandir($absPath) as $entry) {
          if ($entry === '..' || $entry === '.') {
            continue;
          }
          if (is_dir($absPath.$entry)) {
            if (!$isRecursive) {
              continue;
            }
            if ($folder === null) {
              $dirs[]= $entry;
            } else {
              $dirs[]= $folder.'/'.$entry;
            }
          } else {
            $files[] = $entry;
          }
        }
      } else {
        $files[] = basename($folder);
      }
      $classRecognizer = new ClassRecognizer;
      $classes = $classRecognizer->getClasses($files);
      if (count($classes) !== 0) {
        $index = null;
        if ($folder === null) {
          $index = $rootIndex;
        } else {
          $index = count($this->cache[1]);
          if ($rootIndex === null) {
            if ($folder === '.') {
              $index = true;
            } else {
              $this->cache[1][$index] = $folder;
            }
          } else {
            $this->cache[1][$index] = array($folder, $rootIndex);
          }
        }
        foreach ($classes as $class) {
          if (isset($this->cache[0][$class])) {
            throw new CommandException("Conflict class name '$class'.");
          }
          $this->cache[0][$class] = $index;
        }
      }
      if (count($dirs) !== 0) {
        $this->fetch($rootIndex, $dirs);
      }
    }
  }
}