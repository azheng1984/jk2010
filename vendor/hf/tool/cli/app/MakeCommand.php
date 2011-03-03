<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    $this->buildClassLoaderCache();
    if ($this->config['type'] === 'web') {
      $this->buildApplicationCache();
    }
  }

  public function buildClassLoaderCache() {
    $cache = array(array(), array(), array());
    foreach ($this->config['class_path'] as $key => $item) {
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
        //check is class
        $classes[] = preg_replace('/.php$/', '', $entry);
      }
    }
    if (count($classes) !== 0) {
      $index = count($cache[1]);
      $cache[1][$index] = $path;
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
        //add to view
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
        echo $class;
        $actionCache = array('class' => $class, 'method'=>array());
        $reflector = new ReflectionClass($class);
        //reflect method
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