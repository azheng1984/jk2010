<?php
class ClassLoaderConfiguration {
  public function extract($config) {
    if (!is_array($config)) {
      $config = array($config);
    }
    $result = array();
    foreach ($config as $key => $value) {
      if (is_int($key)) {
        list($key, $value) = array($value, array());
      }
      if ($this->isFullPath($key)) {
        $result += array($key, $this->combine(null, $value));
        continue;
      }
      $result += array(null, $this->combine($key, $value));
    }
    return $result;
  }

  private function combine($path, $children) {
    $result = array();
    foreach ($children as $key => $value) {
      $item = null;
      if (!is_int($key)) {
        $item = $key;
      }
      if ($path !== null) {
        $item = $path.DIRECTORY_SEPARATOR.$item;
      }
      if (is_array($value)) {
        $result += $this->combine($item, value);
        continue;
      }
      if (value !== null) {
        $item .= DIRECTORY_SEPARATOR.value;
      }
      $result[] = $item;
    }
    return $result;
  }

  private function isFullPath($path) {
    return $path['0'] === DIRECTORY_SEPARATOR 
      || preg_match('/^[:alpha:]:\\\\/', $path);
  }
}