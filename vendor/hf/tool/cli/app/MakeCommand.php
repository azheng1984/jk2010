<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    if (isset($this->config['class_loader'])) {
      $this->buildClassLoaderCache();
    }
    if (isset($this->config['application'])) {
      $this->buildApplicationCache();
    }
  }

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
      //dispath item to processor like application
      //processor find "target" via suffix.
      //processors add thire own cache (extensible)
      //register processors in make.config.php
      $suffix = substr($entry, -10);
      $entryCache = array();
      if ($suffix === 'Screen.php') {
        if (!isset($pathCache['view'])) {
          $pathCache['view'] = array();
        }
        $pathCache['view']['screen'] = preg_replace('/.php$/', '', $entry);
      }
      if (substr($entry, -9) === 'Image.php') {
        echo $entry;
        if (!isset($pathCache['view'])) {
          $pathCache['view'] = array();
        }
        $pathCache['view']['image'] = preg_replace('/.php$/', '', $entry);
      }
      if ($suffix === 'Action.php') {
        require $dirPath.'/'.$entry;
        $class = preg_replace('/.php$/', '', $entry);
        $actionCache = array('class' => $class, 'method' => array());
        $reflector = new ReflectionClass($class);
        $methods = array();
        foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
          $actionCache['method'][] = $method->name;
        }
        $pathCache['action'] = $actionCache;
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