<?php
namespace Hyperframework;

final class ClassLoader {
    private static $cache;

    public static function run() {
        self::initialize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function initialize() {
        self::$cache = CacheFileLoader::loadPhp(
            'class_loader.php', 'hyperframework.class_loader.cache_path'
        );
//        self::$cache = require APP_ROOT_PATH . '/tmp/cache/class_loader2.php';
    }

    public static function load($name) {
        if (isset(self::$cache['classes'][$name])) {
            require self::$cache['classes'][$name];
            return;
        }
        if (isset(self::$cache['psr4'])) {
            if (self::search(self::$cache['psr4'], explode('\\', $name))) {
                return;
            }
        }
        if (isset(self::$cache['psr0'])) {
            self::search(self::$cache['psr0'], explode('_', $name));
        }
    }

    private static function search(&$node, $segments) {
        $path = null;
        $prefixIndex = null;
        $count = count($segments);
        for ($index = 0; $index < $count; ++$index) {
            if (is_array($node)) {
                if (isset($node[0])) {
                    $path = $node[0];
                    $prefixIndex = $index;
                }
                if (isset($node[$segments[$index]]) === false) {
                    break;
                }
                $node =& $node[$segments[$index]];
            } else {
                $path = $node;
                $prefixIndex = $index;
                break;
            }
        }
        if ($path !== null) {
            while ($prefixIndex < $count) {
                $path .= DIRECTORY_SEPARATOR . $segments[$prefixIndex];
                ++$prefixIndex;
            }
            require $path . '.php';
            return true;
        }
        return false;
    }

    public static function reset() {
        self::$cache = null;
    }
}
