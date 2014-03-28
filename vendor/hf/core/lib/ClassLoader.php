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
        $namespace = null;
        if (strpos('_', $name) !== false) {
            $namespaces = explode('_', $name);
        } else {
            $namespaces = explode('\\', $name);
        }
        $current =& self::$cache;
        $index = 0;
        $basePath = null;
        foreach ($namespaces as $namespace) {
            if (isset($current[$namespace])) {
                ++$index;
                $current =& $current[$namespace];
                continue;
            }
            if (is_array($current)) {
                if (self::$isOneToManyMappingAllowed === false) {
                    $basePath = $current[0];
                    break;
                } else {
                    
                }
            }
            $basePath = $current;
            break;
        }
        //append namespace
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
        self::$cache = ConfigLoader::load(
            'class_loader.php', __CLASS__ . '\CachePath'
        );
    }
}
