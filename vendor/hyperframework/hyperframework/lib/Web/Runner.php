<?php
namespace Hyperframework\Web;

use Hyperframework;
use Hyperframework\Common\EnvironmentBuilder;

class Runner {
    public static function run($appRootNamespace = null, $appRootPath = null) {
        static::initialize($appRootNamespace, $appRootPath);
        static::runApp();
    }

    protected static function initialize($appRootNamespace, $appRootPath) {
        static::initilaizeEnvironment($appRootNamespace, $appRootPath);
        chdir(Hyperframework\APP_ROOT_PATH);
        static::initializeErrorHandler();
    }

    protected static function initilaizeEnvironment(
        $appRootNamespace, $appRootPath
    ) {
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
        if ($appRootPath === null) {
            $appRootPath = dirname(getcwd());
        }
        EnvironmentBuilder::build($appRootNamespace, $appRootPath);
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
