<?php
namespace Hyperframework;

class ClassLoader {
    private static $config;

    public static function run() {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
        echo $name;
        exit;
    }

    protected static function loadConfig() {
        self::$config = ConfigLoader::get(
            'class_loader', __CLASS__ . '\ConfigPath'
        );
    }
}
