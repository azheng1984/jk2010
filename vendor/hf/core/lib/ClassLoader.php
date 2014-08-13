<?php
namespace Hyperframework;

final class ClassLoader {
    private static $cache;

    public static function run() {
        self::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
        if (isset(self::$cache['classes'][$name])) {
            require self::$cache['classes'][$name];
            return;
        }
        if (isset(self::$cache['psr4'])) {
            $node =& self::$cache['psr4'];
            $segments = explode('\\', $name);
            $path = null;
            $prefixIndex = null;
            $count = count($segments);
            for ($index = 0; $index < $count; ++$count) {
                if (is_string($node)) {
                    $path = $node;
                    $prefixIndex = $index;
                } elseif (isset($node[0])) {
                    $path = $node[0];
                    $prefixIndex = $index;
                }
                if (isset($node[$segment]) === false) {
                    if ($path !== null) {
                        ++$prefixIndex;
                        while ($prefixIndex < $count) {
                            $path .= DIRECTORY_SEPARATOR
                                . $segments[$prefixIndex];
                            ++$prefixIndex;
                        }
                        require $path . '.php';
                        return;
                    }
                    break;
                }
                $node =& $node[$segment];
            }
        }
        if (isset(self::$cache['psr0'])) {
            $node =& self::$cache['psr0'];
            $segments = explode('_', $name);
            foreach ($segments as $segment) {
            }
        }
        $segments = null;
        //默认 psr4, 没有找到时降级到 psr0
        if (strpos('_', $name) !== false) {
            $segments = explode('_', $name);
        } else {
            $segments = explode('\\', $name);
        }
        $current =& self::$cache;
        $index = 0;
        $path = null;
        $matches = null;
        $hasOneToManyMapping = self::$hasOneToManyMapping;
        if ($hasOneToManyMapping) {
            $matches = array();
        }
        foreach ($segments as $segment) {
            ++$index;
            if (isset($current[$segment])) {
                $current =& $current[$segment];
                continue;
            }
            // Ns1\Ns2 => '/path' 
            // Ns1\Ns2\List => '/path2' should /path/List when has one to many mapping
            if (is_array($current)) {
                if (isset($current[0]) === false) {
                    return;
                }
                $path = $current[0];
                break;
            }
            $path = $current;
            break;
        }
        if ($path === null) {
            if (isset(self::$cache[0])) {
                $path = $cache[0];
            }
            return;
        }
        $suffix = null;
        while (isset($segments[$index])) {
            $suffix .= DIRECTORY_SEPARATOR . $segments[$index];
            ++$index;
        }
        $suffix .= '.php';
        if ($hasOneToManyMapping && is_array($current[0])) {
            $lastPathIndex = count($current[0]) - 1;
            for ($pathIndex = 0; $pathIndex < $lastPathIndex; ++$pathIndex) {
                $path = $current[0][$pathIndex] . $suffix;
                if (file_exists($path)) {
                    require $path;
                    return;
                }
            }
            $path = $current[0][$lastPathIndex];
        }
        $path .= $suffix;
        if (self::$isFileExistsCheckEnabled === false || file_exists($path)) {
            require $path;
        }
    }

    public static function reset() {
        self::$cache = null;
    }

    private static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'FileLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'FullPathRecognizer.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'CacheFileLoader.php';
        self::$cache = CacheFileLoader::loadPhp(
            'class_loader.php', 'hyperframework.class_loader.cache_path'
        );
    }
}
