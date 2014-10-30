<?php
namespace Hyperframework\Web;

use ArrayAccess;

class View implements ArrayAccess {
    private $model;

    public function render($actionResult) {
        if (is_array($actionResult)) {
            $this->model = $actionResult;
        } else {
            $this->model = array('action_result' => $actionResult);
        }
    }

    public function __invoke($function) {
        $function();
    }
}
