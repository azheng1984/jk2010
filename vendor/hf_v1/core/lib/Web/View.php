<?php
namespace Hyperframework\Web;

use ArrayAccess;

abstract class View implements ArrayAccess {
    private $model;

    public function __construct(array $actionResult = null) {
        if ($actionResult !== null) {
            $this->model = $actionResult;
        } else {
            $this->model = [];
        }
    }

    abstract public function render();

    public function __invoke($function) {
        $function();
    }
}
