<?php
namespace Hyperframework;

class ClassLoader {
    private static $isFileExistsCheckEnabled = false;
    private static $isOneToManyMappingAllowed = false;
    private static $cache;

    final public static function run() {
        static::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    final public static function enableFileExistsCheck() {
        self::$isFileExistsCheckEnabled = true;
    }

    final public static function load($name) {
    }

    protected static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'DataLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'PathTypeRecognizer.php';
        if (Config::get(__CLASS__ . '\EnableCache') === false) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'ConfigLoader.php';
            $config = ConfigLoader::load(
                'class_loader.php', __CLASS__ . '\ConfigPath'
            );
            require __DIR__ . DIRECTORY_SEPARATOR
                . 'ClassLoaderCacheBuilder.php';
            self::$isOneToManyMappingAllowed = true;
            self::$cache = ClassLoaderCacheBuilder::build($config);
            return;
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'CacheLoader.php';
        self::$cache = ConfigLoader::load(
            'class_loader.php', __CLASS__ . '\CachePath'
        );
    }
}
