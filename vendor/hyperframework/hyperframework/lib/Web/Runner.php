<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;
use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    public static function run() {
        static::initialize();
        static::runApp();
    }

    protected static function initializeAppRootPath() {
        Config::set('hyperframework.app_root_path', dirname(getcwd()));
    }

    protected static function initializeErrorHandler() {
        $class = Config::getString('hyperframework.error_handler.class', '');
        if ($class === '') {
            ErrorHandler::run();
        } else {
            if (class_exists($class) === false) {
                throw new ConfigException(
                    "Error handler class '$class' does not exist,"
                        . " defined in 'hyperframework.error_handler.class'."
                );
            }
            $class::run();
        }
    }

    protected static function runApp() {
        $class = Config::getString('hyperframework.web.app_class', '');
        if ($class === '') {
            $app = new App;
        } else {
            if (class_exists($class) === false) {
                throw new ConfigException(
                    "App class '$class' does not exist,"
                        . " defined in 'hyperframework.web.app_class'."
                );
            }
            $app = new $class;
        }
        $app->run();
    }
}
