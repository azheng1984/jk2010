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
            self::searchTree(self::$cache['psr4'], explode('\\', $name));
        }
        if (isset(self::$cache['psr0'])) {
            self::searchTree(self::$cache['psr0'], explode('_', $name));
        }
    }

    private static function searchTree(&$node, $segments) {
        $path = null;
        $prefixIndex = null;
        $count = count($segments);
        for ($index = 0; $index < $count; ++$count) {
            if (is_array($node)) {
                if (isset($node[0])) {
                    $path = $node[0];
                    $prefixIndex = $index;
                }
                if (isset($node[$segment]) === false) {
                    break;
                }
                $node =& $node[$segment];
            } else {
                $path = $node;
                $prefixIndex = $index;
                break;
            }
        }
        if ($path !== null) {
            while ($prefixIndex < $count) {
                $path .= DIRECTORY_SEPARATOR
                    . $segments[$prefixIndex];
                ++$prefixIndex;
            }
            require $path . '.php';
            return;
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
