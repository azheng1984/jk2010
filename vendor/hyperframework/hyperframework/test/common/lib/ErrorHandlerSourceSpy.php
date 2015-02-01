<?php
namespace Hyperframework\Common\Test;

use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandlerSourceSpy extends Base {
    public function displayError() {
        $this->send($this->getError());
    }

    public function send($source) {
    }
}
