<?php
namespace Hyperframework;

final class ClassLoader {
    private static $isFileExistsCheckEnabled = false;
    private static $isOneToManyMappingAllowed = false;
    private static $cache;

    public static function run() {
        self::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function enableFileExistsCheck() {
        self::$isFileExistsCheckEnabled = true;
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
            }
            $path = $current;
            break;
        }
        if ($path === null) {
            if (isset(self::$cache[''])) {
                $path = $cache[''];
            }
            return;
        }
        $suffix = null;
        while (isset($segments[$index])) {
            $suffix .= DIRECTORY_SEPARATOR . $segments[$index];
            ++$index;
        }
        if (self::$isOneToManyMappingAllowed && is_array($current[0])) {
            $lastPathIndex = count($current[0]) - 1;
            for ($pathIndex = 0; $pathIndex < $lastPathIndex; ++$pathIndex) {
                $path .= $suffix;
                if (file_exists($path)) {
                    require $path;
                    return;
                }
            }
            $path = $current[0][$lastPathIndex];
        }
        if (self::$isFileExistsCheckEnabled) {
            if (file_exists($path) === false) {
                return;
            }
        }
        require $path;
    }

    private static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'DataLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'PathTypeRecognizer.php';
        if (Config::get(__CLASS__ . '\EnableCache') === false) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'ConfigLoader.php';
            $config = ConfigLoader::load(
                'class_loader.php', __CLASS__ . '\ConfigPath'
            );
            require __DIR__ . DIRECTORY_SEPARATOR
                . 'ClassLoaderCacheBuilder.php';
            self::$cache = ClassLoaderCacheBuilder::build($config);
            self::$isOneToManyMappingAllowed = true;
            return;
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'CacheLoader.php';
        self::$cache = CacheLoader::load(
            'class_loader.php', __CLASS__ . '\CachePath'
        );
    }
}
