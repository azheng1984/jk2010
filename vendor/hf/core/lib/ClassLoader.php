<?php
namespace Hyperframework;
//  /a/b/c
//  [a] [b] [c]
/*
array(
    '@root_path' => 'a',
    '@path' => 'b',
    'A' => array('@path' => array('x'), '@ignore_path' => 'x/b'),
);
*/

class ClassLoader {
    private static $config;

    public static function run() {
        static::initailize();
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
        $rootPath = Config::getApplicationPath();
        $ignorePaths = array();
        foreach (self::$config as $key => $value) {
            if (strncmp($key, '@', 1) === 0) {
                switch ($key) {
                    case '@root_path':
                        $rootPath = $value;
                        break;
                    case '@ignore_path':
                        $ignorePath[] = $rootPath . $value;
                        break;
                    case '@path':
                        $paths[] = $rootPath . $value;
                        break;
                }
            }
        }
    }

    protected static function initialize() {
        require __DIR__ . DIRECTORY_SEPARATOR . 'DataLoader.php';
        require __DIR__ . DIRECTORY_SEPARATOR . 'ConfigLoader.php';
        self::$config = ConfigLoader::load(
            'class_loader.php', __CLASS__ . '\ConfigPath'
        );
    }

    final protected static function setConfig($value) {
        self::$config = $value;
    }
}
