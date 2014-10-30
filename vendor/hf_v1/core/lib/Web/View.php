<?php
namespace Hyperframework\Web;

use ArrayAccess;

class View implements ArrayAccess {
    private $actionResult;

    public function render($actionResult) {
        if (is_array($actionResult)) {
            $this->actionResult = $actionResult;
        } else {
            $this->actionResult = array('action_result' => $actionResult);
        }
    }

    public function __invoke($function) {
        $function();
    }
}
