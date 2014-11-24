<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\EnvironmentBuilder;

class Runner {
    public static function run() {
        static::initialize();
        static::runApp();
    }

    public static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function initialize($appRootNamespace, $appRootPath) {
        static::initilaizeEnvironment($appRootNamespace, $appRootPath);
        static::initilaizeErrorHandler();
    }

    protected static function initilaizeEnvironment(
        $appRootNamespace, $appRootPath
    ) {
        $hasEnvironmentBuilderClass = class_exists(
            'Hyperframework\Common\EnvironmentBuilder'
        );
        if ($hasEnviromentBuilderClass === false) {
            $commonLibRootPath = null;
            if (basename(__DIR__) === 'Cli') {
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
        if ($appRootPath = null) {
            $appRootPath = getcwd();
        }
        EnvironmentBuilder::build($appRootNamespace, $appRootPath);
    }

    protected function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
