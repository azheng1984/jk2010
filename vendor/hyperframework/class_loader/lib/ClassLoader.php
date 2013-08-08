<?php
namespace Hyperframework;

class ClassLoader {
    private $rootPath;
    private $classes;
    private $folders;

    public function run(
        $rootPath = ROOT_PATH, $cacheDirectoryPath = CACHE_PATH
    ) {
        $this->rootPath = $rootPath;
        //add reader
        $info = require $cacheDirectoryPath . 'class_loader.cache.php';
        $this->classes = $info[0];
        $this->folders = $info[1];
        spl_autoload_register(array($this, 'load'));
    }

    public function stop() {
        spl_autoload_unregister(array($this, 'load'));
    }

    public function load($name) {
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
