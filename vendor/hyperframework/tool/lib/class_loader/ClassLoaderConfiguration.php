<?php
class ClassLoaderConfiguration {
  private $values = array();

  public function extract($config) {
    if (!is_array($config)) {
      $config = array($config);
    }
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        list($key, $value) = array($value, null);
      }
      if ($this->isFullPath($key)) {
        $this->combine($this->format($key), null, $value);
        continue;
      }
      $this->combine(null, $key, $value);
    }
    return $this->values;
  }

  private function combine($rootPath, $relativePath, $children) {
    if ($children === null) {
      $this->values[] = array($rootPath, $this->format($relativePath));
      return;
    }
    foreach ($children as $key => $value) {
      if (is_int($key)) {
        list($key, $value) = array($value, null);
      }
      if ($relativePath !== null) {
        $key = $relativePath.DIRECTORY_SEPARATOR.$key;
      }
      $this->combine($rootPath, $key, value);
    }
  }

  private function isFullPath($path) {
    return $path['0'] === DIRECTORY_SEPARATOR 
      || preg_match('/^[A-Za-z]:\\\\/', $path);
  }

  private function format($path) {
    if ($path === null) {
      return;
    }
    $search = DIRECTORY_SEPARATOR === '/' ? '\\' : '/';
    return str_replace($search, DIRECTORY_SEPARATOR, $path);
  }
}