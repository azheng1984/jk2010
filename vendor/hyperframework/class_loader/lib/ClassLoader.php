<?php
namespace Hyperframework;
//通过 apc 缓存 cache array
class ClassLoader {
    private $rootPath;
    private $classes;
    private $folders;

    public function run($rootPath = null, $cachePath = null) {
        if ($rootPath === null) {
            $rootPath = ROOT_PATH;
        }
        if ($cachePath === null) {
            $cachePath = CACHE_PATH . 'class_loader.cache.php';
        }
        $this->rootPath = $rootPath;
        $cache = require $cachePath;
        //var_dump($info);
        $this->classes = $cache[0];
        $this->folders = $cache[1];
        spl_autoload_register(array($this, 'load'));
    }

    public function stop() {
        spl_autoload_unregister(array($this, 'load'));
    }

    public function load($name) {
        //use apc cache
//        echo '>' . $name . PHP_EOL;
        $tmp = explode('\\', $name);
        $name = end($tmp);
        if (isset($this->classes[$name])) {
           // echo $this->getFolder($this->classes[$name]) . $name . '.php'.PHP_EOL;
            require $this->getFolder($this->classes[$name]) . $name . '.php';
        }
    }

    private function getFolder($index) {
        if ($index === true) {
            return $this->rootPath;
        }
        $folder = $this->folders[$index];
        if (is_array($folder)) {
            return $this->getFullPath($folder) .
                $folder[0] . DIRECTORY_SEPARATOR;
        }
        return $this->rootPath . $folder . DIRECTORY_SEPARATOR;
    }

    private function getFullPath($folder) {
        if (isset($folder[1])) {
            return $this->folders[$folder[1]][0] . DIRECTORY_SEPARATOR;
        }
    }
}
