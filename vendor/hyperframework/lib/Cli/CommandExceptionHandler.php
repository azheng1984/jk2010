<?php
class CommandExceptionHandler {
    public function run() {
        set_exception_handler(array($this, 'handle'));
    }

    public function stop() {
        restore_exception_handler();
    }

    public function handle($exception) {
        fwrite(STDERR, $exception . PHP_EOL);
        exit($exception->getCode());
    }
}
