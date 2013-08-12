<?php
class ApplicationCache {
    private $cache;
    private $errorCache;

    public function __construct($handlers) {
//        $processors = array();
//        foreach ($handlers as $key => $value) {
//            $processors[$key] = $key.'Processor';
//        }
//        $this->cache = array($processors);
          $this->cache = array();
          $this->errorCache = array();
    }

    public function append($relativeFolder, $name, $cache) {
        $path = DIRECTORY_SEPARATOR.$relativeFolder;
        if ($path !== '/' && $this->hasChild($path)) {
            $path .= '/';
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        }
        //todo: duplicate code
        if (strpos($path, '/error') === 0) {
            if (!isset($this->errorCache[$path])) {
                $this->errorCache[$path] = array($name => $cache);
                return;
            }
            if (!isset($this->errorCache[$path][$name])) {
                $this->errorCache[$path][$name] = $cache;
                return;
            }
            if (!is_array($this->errorCache[$path][$name])) {
                $this->errorCache[$path][$name] = array($this->errorCache[$path][$name]);
            }
            if (!is_array($cache)) {
                $cache = array($cache);
            }
            $this->errorCache[$path][$name] = array_merge(
                $cache, $this->errorCache[$path][$name]
            );
            return;
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
        return array(
            'application' => $this->cache,
            'application.error' => $this->errorCache
        );
    }
}
