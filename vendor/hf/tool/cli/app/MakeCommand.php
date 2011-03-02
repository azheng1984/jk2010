<?php
class MakeCommand {
  private $config;

  public function execute() {
    $this->config = require 'config/make.config.php';
    $this->buildClassLoaderCache();
    if ($this->config['type'] === 'web') {
      //$this->buildApplicationCache();
    }
  }

  public function buildClassLoaderCache() {
    $cache = array(array(), array(), array());
    foreach ($this->config['class_path'] as $key => $item) {
      $root = realpath($key);
      foreach ($item as $path) {
        $dirPath = $root.'/'.$path;
        $this->buildDir($dirPath, $cache);
      }
    }
    file_put_contents('cache/class_loader.cache.php', "<?php\nreturn ".$this->renderArray($cache).";");
  }
  
  private function buildDir($dirPath, &$cache) {
    $dir = dir($dirPath);
    $classes = array();
    while (false !== ($entry = $dir->read())) {
      if ($entry === '..' || $entry === '.') {
        continue;
      }
      if (is_dir($dirPath.'/'.$entry)) {
        $this->buildDir($dirPath.'/'.$entry, $cache);
      }
      //check is class
      $classes[] = $entry;
    }
    if (count($classes) !== 0) {
      $index = count($cache[1]);
      $cache[1][$index] = $dirPath;
      foreach ($classes as $class) {
        $cache[0][$class] = $index;
      }
    }
  }

  private function getClass() {
    
  }

  public function buildApplicationCache() {
    //scan app file and reflect class
    $dir = dir('app');
    while (false !== ($item = $dir->read())) {
      //dispath item to processor like application
      //processor find "target" via suffix.
      //processors add thire own cache (extensible)
      //register processors in make.config.php
      $suffix = substr($item, -10);
      if ($suffix === 'Screen.php') {
        //add to view
      }
      if ($suffix === 'Action.php') {
        require $item;
        $tmp = explode('.', $item, 2);
        $reflector = new ReflectionClass($tmp[0]);
        //reflect method
        $methods = array();
        foreach ($reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
          $method[] = $method['name'];
        }
      }
    }
    file_put_contents('cache/application.cache.php', "<?php\nreturn ".var_export(array(0=>array(), 1=>array()), true));
  }

  private function renderArray($value) {//configurable formatter
    return var_export($value, true);
  }
}