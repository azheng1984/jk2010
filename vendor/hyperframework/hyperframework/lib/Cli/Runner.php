<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\EnvironmentBuilder;

class Runner {
    private static $appRootPath;

    public static function run($appRootPath = null) {
        if ($appRootPath === null) {
            $appRootPath = getcwd();
        }
        static::initialize($appRootPath);
        static::runApp();
    }

    public static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function initialize($appRootPath) {
        static::initilaizeEnvironment($appRootPath);
        static::initializeErrorHandler();
    }

    protected static function initilaizeEnvironment($appRootPath) {
        $hasEnvironmentBuilderClass = class_exists(
            'Hyperframework\Common\EnvironmentBuilder'
        );
        if ($hasEnvironmentBuilderClass === false) {
            $commonLibRootPath = null;
            if (basename(__DIR__) === 'Cli') {
                $commonLibRootPath =
                    dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Common';
            } else {
                $commonLibRootPath =
                    dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
                        . 'common' . DIRECTORY_SEPARATOR . 'lib';
            }
            require $commonLibRootPath
                . DIRECTORY_SEPARATOR . 'EnvironmentBuilder.php';
        }
        EnvironmentBuilder::build($appRootPath);
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
