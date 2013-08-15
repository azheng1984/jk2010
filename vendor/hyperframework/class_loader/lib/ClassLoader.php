<?php
namespace Hyperframework;

class ClassLoader {
    private $rootPath;
    private $cache;

    public function run($rootPath = null, $cachePath = null) {
        if ($rootPath === null) {
            $rootPath = ROOT_PATH;
        }
        $this->rootPath = $rootPath;
        if ($cachePath === null) {
            $cachePath = CACHE_PATH . 'class_loader.cache.php';
        }
        $this->cache = require $cachePath;
        //var_dump($info);
        spl_autoload_register(array($this, 'load'));
    }

    public function stop() {
        spl_autoload_unregister(array($this, 'load'));
    }

    public function load($name) {
//        echo $name . '<br/>';
//        echo '>' . $name . PHP_EOL;
        $namespaces = explode('\\', $name);
        $class = array_pop($namespaces);
        $info = $this->cache;
        $index = 0;
        //var_dump($namespaces);
        //var_dump($this->cache);
        foreach ($namespaces as $namespace) {
            if (isset($info[$namespace])) {

            //echo '>'.$namespace;
                $info = $info[$namespace];
                ++$index;
                continue;
            }
            break;
        }
        //echo $name;
        //var_dump($info);
        $amount = count($namespaces);
        if ($amount !== $index || isset($info['@classes']) === false) {
            $path = $info;
            if (is_array($info)) {
                $path = $info[0];
            }
            for ($index; $index < $amount; ++$index) {
                $path .= '/' . $namespaces[$index];
            }
            //echo $path . '/'. $class . '.php';
            require $path . '/'. $class . '.php';
        } else {
////            echo '@@@@' . $name;
//            var_dump($info);
//            echo '###';
            if (isset($info['@classes'][0][$class])) {
                $this->classes = $info['@classes'][0];
                $this->folders = $info['@classes'][1];
                //echo $this->getFolder($this->classes[$class]) . $class . '.php'.PHP_EOL;
                require $this->getFolder($this->classes[$class]) . $class . '.php';
            }
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
