<?php
namespace Hyperframework\Web\Build;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Cli\ExceptionHandler;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        //class_loader cache
        //asset cache
        //path_info cache
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }
}
