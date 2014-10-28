<?php
namespace Hyperframework;

final class ClassLoader {
    private static $rootPath;
    private static $isZeroFolderEnabled;

    public static function run() {
        self::initialize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function initialize() {
        self::$rootPath = Config::get(
            'hyperframework.class_loader.root_path'
        );
        if (self::$rootPath === null) {
            self::$rootPath = APP_ROOT_PATH . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'lib';
        }
        self::$isZeroFolderEnabled = Config::get(
            'hyperframework.class_loader.enable_zero_folder'
        ) === true;
    }

    public static function load($name) {
        if (self::$isZeroFolderEnabled && strpos($name, '\\') === false) {
            require self::$rootPath . DIRECTORY_SEPARATOR . '0'
                . DIRECTORY_SEPARATOR
                . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
            return;
        }
        require self::$rootPath . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, $name) . '.php';
    }

    public static function reset() {
        self::$rootPath = null;
        self::$isZeroFolderEnabled = null;
    }
}
