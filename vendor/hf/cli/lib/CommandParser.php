<?php
//bin-name [method] -option args command_args
class CommandParser {
  public function run() {
    $mapping = require CONFIG_PATH.__CLASS__.'.config.php';
    global $argv;

    $method = null;
    $path = null;
    if (isset($argv[1])) {
      $method = $argv[1];
      if (isset($mapping[$method])) {
        $methodMapping = $mapping[$method];
        if (isset($methodMapping['method'])) {
          $method = $methodMapping['method'];
        }
        if (isset($methodMapping['path'])) {
          $path = $methodMapping['path'];
        }
      }
    }

    if (count($argv) > 2 ) {
      $args = array();
      if ($path == null && !$this->startsWith($argv[2], '-')) {
        $path = $argv[2];
        $args = array_slice($argv, 3);
      } else {
        $args = array_slice($argv, 2);
      }
      $key = null;
      foreach ($args as $arg) {
        if ($this->startsWith($arg, '-')) {
          if ($key != null) {
            $_GET[$key] = true;
          }
          $key = preg_replace('/^--?/', '', $arg);
        } elseif ($key != null) {
          $_GET[$key] = $arg;
          $key = null;
        } else {
          throw new Exception('Argument parser error'.print_r($argv, true));
        }
      }
      if ($key != null) {
        $_GET[$key] = true;
      }
    }
    $_SERVER['REQUEST_METHOD'] = $method;
    echo $method."\r\n";
    echo $path."\r\n";
    print_r($_GET);
    $path = 'project';
    return $path;
  }
  
  public function startsWith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
}
}