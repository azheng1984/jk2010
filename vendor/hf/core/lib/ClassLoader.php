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
