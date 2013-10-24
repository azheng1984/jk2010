<?php
namespace Hyperframework;

class ClassLoader2 {
    private $rootPath;
    private $callback;
    private $classes;
    private $folders;

    public function run($rootPath = ROOT_PATH, $cachePath = CACHE_PATH) {
        list($this->classes, $this->folders) = require(
            $cachePath . 'class_loader.cache.php'
        );
        $this->rootPath = $rootPath;
        $this->callback = array($this, 'load');
        spl_autoload_register($this->callback);
    }

    public function stop() {
        spl_autoload_unregister($this->callback);
    }

    public function load($name) {
        if ($this->startsWith($name, 'Hyperframework\Tool\App')) {
            require HYPERFRAMEWORK_PATH . 'tool/app/' . substr($name, strlen('Hyperframework/Tool/App'));
            return;
        }
        if ($this->startsWith($name, 'Hyperframework\Tool')) {
            require HYPERFRAMEWORK_PATH . 'tool/lib/' . substr($name, strlen('Hyperframework/Tool'));
            return;
        }
        if ($this->startsWith($name, 'Hyperframework')) {
            require HYPERFRAMEWORK_PATH . 'lib/' . substr($name, strlen('Hyperframework'));
            return;
        }
        if (isset($this->classes[$name])) {
            require(
                $this->getFolder($this->classes[$name]).$name.'.php'
            );
        }
    }

    function startsWith($haystack, $needle) {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    private function getFolder($index) {
        if ($index === true) {
            return $this->rootPath;
        }
        $folder = $this->folders[$index];
        if (is_array($folder)) {
            return $this->getFullPath($folder) . $folder[0] .
DIRECTORY_SEPARATOR;
        }
        return $this->rootPath . $folder . DIRECTORY_SEPARATOR;
    }

    private function getFullPath($folder) {
        if (isset($folder[1])) {
            return $this->folders[$folder[1]][0].DIRECTORY_SEPARATOR;
        }
    }
}
