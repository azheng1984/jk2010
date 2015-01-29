<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    public static function run() {
        $appRootPath = dirname(getcwd());
        $runner = new Runner($appRootPath);
        $runner->runApp();
    }

    protected function runApp() {
        $class = Config::getString('hyperframework.web.app_class', '');
        if ($class === '') {
            $app = new App;
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "App class '$class' does not exist,"
                        . " defined in 'hyperframework.web.app_class'."
                );
            }
            $app = new $class;
        }
        $app->run();
    }

    protected function getDefaultErrorHandler() {
        return new ErrorHandler;
    }
}
