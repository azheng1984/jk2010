<?php
namespace Hyperframework\Web;

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'AbstractInitializer.php';

class Initializer extends \Hyperframework\AbstractInitializer {
    protected static function initializeExceptionHandler() {
        ExcpetionHander::run();
    }
}
