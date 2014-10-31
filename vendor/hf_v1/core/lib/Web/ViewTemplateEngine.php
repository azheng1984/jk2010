<?php
namespace Hyperframework\Web;

use ArrayAccess;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $blocks;

    public function __construct(array $model = null) {
        if ($model !== null) {
            $this->model = $model;
        } else {
            $this->model = [];
        }
    }

    abstract public function render();

    public function renderBlock($name, $default = null) {
    }

    public function setBlock($name, $value) {
    }

    public function extend($layout) {
    }

    public function setLayout() {
    }

    public function __invoke($function) {
        $function();
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new Exception;
        } else {
            $this->model[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->model[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->model[$offset]);
    }

    public function offsetGet($offset) {
        return $this->model[$offset];
    }
}
