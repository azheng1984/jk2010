<?php
namespace Hyperframework;
//通过 apc 缓存 cache array
class ClassLoader {
    private $rootPath;
    private $classes;
    private $folders;

    public function run(
        $rootPath = ROOT_PATH, $cacheDirectory = CACHE_PATH
    ) {
        $this->rootPath = $rootPath;
        $info = require $cacheDirectory . 'class_loader.cache.php';
        $this->classes = $info[0];
        $this->folders = $info[1];
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
            require(
                $this->getFolder($this->classes[$name]) . $name . '.php'
            );
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
