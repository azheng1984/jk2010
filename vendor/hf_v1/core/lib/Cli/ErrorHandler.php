<?php
namespace Hyperframework\Cli;

class ErrorHandler {
    public static function run() {
        set_exception_handler(array(__CLASS__, 'handle'));
    }

    public static function stop() {
        restore_exception_handler();
    }

    public static function handle($exception) {
        fwrite(STDERR, $exception->getMessage() . PHP_EOL);
        exit(1);
    }
}
