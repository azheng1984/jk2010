<?php
namespace Hyperframework;

final class ClassLoader {
    private static $isFileExistsCheckEnabled = false;
    private static $hasOneToManyMapping = false;
    private static $cache;

    public static function run() {
        self::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
        $segments = null;
        if (strpos('_', $name) !== false) {
            $segments = explode('_', $name);
        } else {
            $segments = explode('\\', $name);
        }
        $current =& self::$cache;
        $index = 0;
        $path = null;
        foreach ($segments as $segment) {
            ++$index;
            if (isset($current[$segment])) {
                $current =& $current[$segment];
                continue;
            }
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
        if (self::$isOneToManyMappingAllowed && is_array($current[0])) {
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

    public static function enableFileExistsCheck() {
        self::$isFileExistsCheckEnabled = true;
    }

    public static function addCache($cache) {
        if (ClassLoaderCacheBuilder::merge(self::$cache, $cache)) {
            self::$hasOneToManyMapping = true;
        };
    }

    public static function addConfig($config) {
        if (ClassLoaderCacheBuilder::build(self::$cache, $config)) {
            self::$hasOneToManyMapping = true;
        };
    }

    public static function reset() {
        self::$isFileExistsCheckEnabled = false;
        self::$hasOneToManyMapping = false;
        self::$cache = null;
    }

    private static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'PhpDataFileLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'PathTypeRecognizer.php';
        if (Config::get('hyperframework.class_loader.enable_cache') !== false) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'PhpCacheFileLoader.php';
            self::$cache = PhpCacheFileLoader::load(
                'class_loader.php', 'hyperframework.class_loader.cache_path'
            );
            return;
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'PhpConfigFileLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'ClassLoaderCacheBuilder.php';
        $config = PhpConfigFileLoader::load(
            'class_loader.php', 'hyperframework.class_loader.config_path'
        );
        if ($config !== null) {
            self::addConfig($config);
        }
    }
}
