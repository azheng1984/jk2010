<?php
namespace Hyperframework\Web;

use ArrayAccess;
use Exception;
use Hyperframework\FullPathRecognizer;
use Hyperframework\Config;
use Hyperframework;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $blocks = [];
    private $layout;
    private $viewRootPath;

    public function __construct(array $model = null) {
        if ($model !== null) {
            $this->model = $model;
        } else {
            $this->model = [];
        }
    }

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

    public function setLayout($path) {
        $this->layout = $path;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function render($name) {
        $this->renderTemplate($name);
        if ($this->getLayout() !== null) {
            $this->renderLayout();
        }
    }

    abstract protected function renderTemplate($path);

    protected function renderLayout($path) {
        if ($path === null) {
            $path = $this->getLayout();
            if ($path === null) {
                throw new Exception;
            }
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = $this->getViewRootPath() . DIRECTORY_SEPARATOR
                . '_layouts' . DIRECTORY_SEPARATOR . $path;
        }
        $this->renderTemplate($path);
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
