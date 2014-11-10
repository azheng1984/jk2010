<?php
namespace Hyperframework\Web;

use ArrayAccess;
use Exception;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $blocks = [];
    private $layout;

    public function __construct(array $model = null) {
        if ($model !== null) {
            $this->model = $model;
        } else {
            $this->model = [];
        }
    }

    abstract public function render($name);

    public function renderBlock($name, $default = null) {
        if (isset($this->blocks[$name])) {
            $function = $this->blocks[$name];
            $function();
        } else {
            if ($default === null) {
                throw new Exception("View block '$name' not found");
            }
            $default();
        }
    }

    public function setBlock($name, $function) {
        $this->blocks[$name] = $function;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function getLayout() {
        return $this->layout;
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
