<?php
namespace Hyperframework\Web;

require dirname(__DIR__) . DIRECTORY_SEPARATOR
    . 'AbstractEnvironmentBuilder.php';

class EnvironmentBuilder extends \Hyperframework\AbstractEnvironmentBuilder {
    protected static function initializeExceptionHandler() {
        ExceptionHandler::run();
    }
}
