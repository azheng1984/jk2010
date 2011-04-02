<?php
class CommandErrorHandler {
  public function run() {
    set_exception_handler(array($this, 'handle'));
  }

  public function stop() {
    restore_exception_handler();
  }

  public function handle($exception) {
    fwrite(STDERR, $exception);
    exit($exception->getCode());
  }
}