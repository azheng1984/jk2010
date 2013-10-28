<?php
namespace Hyperframework\Cli;

class ExceptionHandler {
    public static function run() {
        set_exception_handler(array(__CLASS__, 'handle'));
    }

    public static function stop() {
        restore_exception_handler();
    }

    public static function handle($exception) {
        fwrite(STDERR, $exception . PHP_EOL);
        exit($exception->getCode());
    }
}
