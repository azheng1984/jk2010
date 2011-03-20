<?php
class DirectoryScanner {
  private $classRecognizer;
  
  public function __construct($classRecognizer) {
    $this->classRecognizer = $classRecognizer;
  }

  private function getFiles($index, $path) {
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
      $classes = $this->classRecognizer->getClasses($files);
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