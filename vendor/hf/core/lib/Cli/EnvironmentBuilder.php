<?php
namespace Hyperframework\Cli;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'AbstractEnviromentBuilder.php';

class EnviromentBuilder {
    protected static function initializeExceptionHandler() {
        ExcpetionHandler::run();
    }
}
