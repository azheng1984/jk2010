<?php
class ApplicationCache {
    private $cache;

    public function __construct($handlers) {
//        $processors = array();
//        foreach ($handlers as $key => $value) {
//            $processors[$key] = $key.'Processor';
//        }
//        $this->cache = array($processors);
          $this->cache = array();
    }

    public function append($relativeFolder, $name, $cache) {
        $path = DIRECTORY_SEPARATOR.$relativeFolder;
        if ($path !== '/' && $this->hasChild($path)) {
            $path .= '/';
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        if (!isset($this->cache[$path])) {
            $this->cache[$path] = array($name => $cache);
            return;
        }
        if (!isset($this->cache[$path][$name])) {
            $this->cache[$path][$name] = $cache;
            return;
        }
        if (!is_array($this->cache[$path][$name])) {
            $this->cache[$path][$name] = array($this->cache[$path][$name]);
        }
        if (!is_array($cache)) {
            $cache = array($cache);
        }
        $this->cache[$path][$name] = array_merge(
            $cache, $this->cache[$path][$name]
        );
    }

    private function hasChild($path) {
        $path = $_SERVER['PWD'] . '/' . 'app' . $path . '/';
        $files = scandir($path); 
        foreach ($files as $key => $value) {
            if ($value === '.' || $value === '..') {
                continue;
            }
            if (is_dir($path . $value)) {
                return true;
            }
        }
        return false;
    }


    public function export() {
        return array('application', $this->cache);
    }
}
