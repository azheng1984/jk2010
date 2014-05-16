<?php
namespace Hyperframework\Cli;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'AbstractInitializer.php';

class Initializer {
    protected static function initializeExceptionHandler() {
        ExcpetionHandler::run();
    }
}
