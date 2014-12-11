<?php
namespace Hyperframework\Cli;

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
        $appRootPath = (string)self::$appRootPath;
        if ($appRootPath === '') {
            throw new Exception;
        }
        Config::set('hyperframework.app_root_path', $appRootPath);
    }

    protected static function runApp() {
        $class = (string)Config::get('hyperframework.cli.app_class');
        if ($class === '') {
            $app = new App;
        } else {
            $app = new $class;
        }
        $app->run();
    }
}
