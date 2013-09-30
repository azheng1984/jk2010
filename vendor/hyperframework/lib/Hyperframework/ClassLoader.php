<?php
namespace Hyperframework;

class ClassLoader {
    private static $rootPath;
    private static $cache;
    private static $classes;
    private static $folders;

    public static function run($rootPath = null, $cachePath = null, $mode = null) {
        if ($rootPath === null) {
            $rootPath = ROOT_PATH;
        }
        static::$rootPath = $rootPath;
        if ($cachePath === null) {
            $cachePath = CACHE_PATH . 'class_loader.cache.php';
        }
        if ($mode === null) {
            $mode = MODE;
        }
        if ($mode === 'development') {
            //no cache
        }
        static::$cache = require $cachePath;
        //var_dump($info);
        spl_autoload_register(array(get_called_class(), 'load'));
    }

    public static function stop() {
        spl_autoload_unregister(array(get_called_class(), 'load'));
    }

    public static function reset() {
        static::$rootPath = null;
        static::$cache = null;
    }

    public static function load($name) {
//        echo $name . '<br/>';
//        echo '>' . $name . PHP_EOL;
        $namespaces = explode('\\', $name);
        $class = array_pop($namespaces);
        $info = static::$cache;
        $index = 0;
        //var_dump($namespaces);
        //var_dump(static::$cache);
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
        //echo $index;
        if ($amount !== $index || isset($info['@classes'][0][$class]) === false) {
            $path = $info;
            if (is_array($info)) {
                if (isset($info[0])) {
                    $path = $info[0];
                } else {
                    return;
                }
            } elseif (is_string($path) === false) {
                return;
            }
            for ($index; $index < $amount; ++$index) {
                $path .= '/' . $namespaces[$index];
            }
//            echo $path . '/'. $class . '.php' . PHP_EOL;
//            echo $path.PHP_EOL;
//            echo $class;
            require $path . '/'. $class . '.php';
        } else {
           // echo '!@!';
//            echo '@@@@' . $name;
//            var_dump($info);
//            echo '###';
            static::$classes = $info['@classes'][0];
            static::$folders = $info['@classes'][1];
            //echo static::$getFolder($this->classes[$class]) . $class . '.php'.PHP_EOL;
            require static::getFolder(static::$classes[$class]) . $class . '.php';
            /* elseif (isset($info[0])) {
                require $info[0] . '/' . $class . '.php';
            } elseif (is_string($info)) {
                require $info . '/' . $class . '.php';
            }*/
        }
    }

    private static function getFolder($index) {
        if ($index === true) {
            return static::$rootPath;
        }
        $folder = static::$folders[$index];
        if (is_array($folder)) {
            return static::getFullPath($folder) .
                $folder[0] . DIRECTORY_SEPARATOR;
        }
        return static::$rootPath . $folder . DIRECTORY_SEPARATOR;
    }

    private static function getFullPath($folder) {
        if (isset($folder[1])) {
            return static::$folders[$folder[1]][0] . DIRECTORY_SEPARATOR;
        }
    }
}
