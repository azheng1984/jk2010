<?php
namespace Hyperframework;

class ClassLoader {
    private static $hasMultipleTarget = false;
    private static $cache;

    public static function run() {
        static::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
    }

    protected static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'DataLoader.php';
        if (Config::get(__CLASS__ . '\EnableCache') === false) {
            self::$hasMultipleTarget = true;
            require __DIR__ . DIRECTORY_SEPARATOR . 'ConfigLoader.php';
            $config = ConfigLoader::load(
                'class_loader.php', __CLASS__ . '\ConfigPath'
            );
            self::initializeCache($config);
        }
        require __DIR__ . DIRECTORY_SEPARATOR . 'CacheLoader.php';
        self::$cache = ConfigLoader::load(
            'class_loader.php', __CLASS__ . '\CachePath'
        );
    }

    private static function initializeCache($config) {
        $rootPath = Config::getApplicationPath();
        $cache = array();
        foreach ($config as $key => $value) {
            $namespaces = explode('\\', $key);
            $current =& $cache;
            foreach ($namespaces as $namespace) {
                if (isset($current[$namespace]) === false) {
                    $current[$namespace] = array();
                    unset($current);
                }
            }
            end($namespaces);
        }
        self::$cache = $cache;
    }
}
