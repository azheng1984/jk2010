<?php
namespace Hyperframework\Common\Test;

use Hyperframework\Common\ErrorHandler as Base;

class ErrorTriggeredErrorHandler extends Base {
    protected function displayError() {
        trigger_error('notice');
    }

    public function getFile() {
        return __FILE__;
    }

    public function getErrorLine() {
        return 8;
    }
}
