<?php
namespace Hyperframework\Web;

use ArrayAccess;

class View implements ArrayAccess {
    private $actionResult;

    public function render($actionResult) {
        $this->actionResult = $actionResult;
    }

    public function __invoke($function) {
        $function();
    }
}
