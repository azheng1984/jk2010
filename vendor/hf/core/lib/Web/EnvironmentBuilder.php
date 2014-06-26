<?php
namespace Hyperframework\Web;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'EnvironmentBuilder.php';

class EnvironmentBuilder extends \Hyperframework\EnvironmentBuilder {
    public static function run($rootNamespace, $rootPath) {
        parent::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }
}
