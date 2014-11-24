<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\EnvironmentBuilder;

class Runner {
    public static function run($appRootPath = null) {
        if ($appRootPath === null) {
            $appRootPath = dirname(getcwd());
        }
        static::initialize($appRootPath);
        static::runApp();
    }

    protected static function initialize($appRootPath) {
        static::initilaizeEnvironment($appRootPath);
        chdir(Config::get('hyperframework.app_root_path'));
        static::initializeErrorHandler();
    }

    protected static function initilaizeEnvironment($appRootPath) {
        $hasEnvironmentBuilderClass = class_exists(
            'Hyperframework\Common\EnvironmentBuilder'
        );
        if ($hasEnvironmentBuilderClass === false) {
            $commonLibRootPath = null;
            if (basename(__DIR__) === 'Web') {
                $commonLibRootPath =
                    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Common';
            } else {
                $commonLibRootPath =
                    dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
                        . 'common' . DIRECTORY_SEPARATOR
                        . 'lib';
            }
            require $commonLibRootPath
                . DIRECTORY_SEPARATOR . 'EnvironmentBuilder.php';
        }
        EnvironmentBuilder::build($appRootPath);
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
