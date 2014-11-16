<?php
namespace Hyperframework\Web;

use ArrayAccess;
use Exception;
use Hyperframework;
use Hyperframework\FullPathRecognizer;
use Hyperframework\Config;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $blocks = [];
    private $viewRootPath;
    private $pathStack = [];
    private $optionStack = [];

    public function __construct(array $model = null) {
        if ($model !== null) {
            $this->model = $model;
        } else {
            $this->model = [];
        }
    }

    abstract protected function render($path, array $options = null);

    protected function renderBlock($name, $default = null) {
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

    protected function pushOptions($options) {
    }

    protected function popOptions() {
    }

    protected function getOptions() {
    }

    protected function setBlock($name, $function) {
        //$this->blocks[$name] = array($function, $this->getPath());
        $this->blocks[$name] = $function;
    }

    protected function getViewRootPath() {
        if ($this->viewRootPath === null) {
            $path = Config::get('hyperframework.web.view.root_path');
            if ($path === null) {
                $path = Hyperframework\APP_ROOT_PATH
                    . DIRECTORY_SEPARATOR . 'views';
            } else {
                if (FullPathRecognizer::isFull($path) === false) {
                    $path = Hyperframework\APP_ROOT_PATH
                        . DIRECTORY_SEPARATOR . $path;
                }
            }
            $this->viewRootPath = $path;
        }
        return $this->viewRootPath;
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
