<?php
namespace Hyperframework\Web;

use ArrayAccess;

class View implements ArrayAccess {
    private $model;

    public function __construct(array $actionResult = null) {
        if ($actionResult !== null) {
            $this->model = $actionResult;
        } else {
            $this->model = [];
        }
    }

    public function render() {
        //render default template if exist
    }

    public function __invoke($function) {
        $function();
    }
}
