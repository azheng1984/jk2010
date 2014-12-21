<?php
namespace Hyperframework\Cli;

use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    private static $appRootPath;

    public static function run($appRootPath) {
        self::$appRootPath = $appRootPath;
        static::initialize();
        static::runApp();
    }

    protected static function initializeAppRootPath() {
        Config::set('hyperframework.app_root_path', self::$appRootPath);
    }

    protected static function runApp() {
        $class = Config::getString('hyperframework.cli.app_class', '');
        if ($class === '') {
            $app = new App;
        } else {
            if (class_exists($class) === false) {
                throw new Exception;
            }
            $app = new $class;
        }
        $app->run();
    }
}
