<?php
namespace Hyperframework\Web\Build;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Cli\ExceptionHandler;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        //read option
        //class_loader cache
        //path_info cache
        //asset cache
        self::buildPathInfoCache();
    }

    private static function buildPathInfoCache() {
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }
}
