<?php
namespace Hyperframework;

final class ClassLoader {
    private static $cacheRootPath;
    private static $isZeroFolderEnabled;

    public static function run() {
        self::initialize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function initialize() {
        self::$cacheRootPath = Config::get(
            'hyperframework.class_loader.cache_root_path'
        );
        if (self::$cacheRootPath === null) {
            self::$cacheRootPath = APP_ROOT_PATH . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'lib';
        }
        if (Config::get('hyperframework.class_loader.enable_zero_folder')
            === true
        ) {
            self::$isZeroFolderEnabled = true;
        } else {
            self::$isZeroFolderEnabled = false;
        }
    }

    public static function load($name) {
        var_dump(self::$isZeroFolderEnabled);
        if (self::$isZeroFolderEnabled && strpos($name, '\\') === false) {
            require self::$cacheRootPath . DIRECTORY_SEPARATOR . '0'
                . DIRECTORY_SEPARATOR
                . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
            return;
        }
        require self::$cacheRootPath . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
    }

    public static function reset() {
        self::$cacheRootPath = null;
        self::$isZeroFolderEnabled = null;
    }
}
