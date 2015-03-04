<?php
namespace Hyperframework\Web;

use ArrayAccess;
use InvalidArgumentException;
use Closure;
use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;
use Hyperframework\Common\FullPathRecognizer;
use Hyperframework\Common\PathCombiner;

abstract class ViewTemplate implements ArrayAccess {
    private $viewModel;
    private $loadFileFunction;
    private $blocks = [];
    private $layoutStack = [];
    private $rootPath;

    public function __construct($loadFileFunction, array $viewModel = null) {
        $this->loadFileFunction = $loadFileFunction;
        $this->viewModel = $viewModel === null ? [] : $viewModel;
    }

    public function render($path) {
        $path = (string)$path;
        if ($path === '') {
            throw new ViewException('View path cannot be empty.');
        }
        $this->pushLayout();
        $this->setLayout(null);
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        if (FullPathRecognizer::isFull($path)) {
            $this->fullPath = $path;
        } else {
            PathCombiner::prepend($path, $this->getRootPath());
            $this->fullPath = $path;
        }
        $loadFileFunction = $this->loadFileFunction;
        $loadFileFunction($this->fullPath);
        if ($this->layoutPath !== null) {
            $this->render($this->layoutPath);
        }
        $this->popLayout();
    }

    public function setLayout($path) {
        $this->layoutPath = $path;
    }

    public function renderBlock($name, Closure $default = null) {
        $this->pushLayout();
        if (isset($this->blocks[$name])) {
            $function = $this->blocks[$name];
            $function();
        } else {
            if ($default === null) {
                throw new ViewException("Block '$name' does not exist.");
            }
            $default();
        }
        $this->popLayout();
    }

    public function setBlock($name, Closure $function) {
        $this->blocks[$name] = $function;
    }

    public function __invoke($function) {
        return $function();
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new InvalidArgumentException('Offset cannot be null.');
        } else {
            $this->viewModel[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->viewModel[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->viewModel[$offset]);
    }

    public function offsetGet($offset) {
        if (isset($this->viewModel[$offset]) === false) {
            throw new ViewException(
                "View model field '$offset' is not defined."
            );
        }
        return $this->viewModel[$offset];
    }

    private function getRootPath() {
        if ($this->rootPath === null) {
            $path = Config::getString(
                'hyperframework.web.view.root_path', ''
            );
            if ($path === '') {
                $path = 'views';
            }
            $this->rootPath = FileLoader::getFullPath($path);
        }
        return $this->rootPath;
    }

    private function pushLayout() {
        array_push($this->layoutStack, $this->layoutPath);
    }

    private function popLayout() {
        $this->layoutPath = array_pop($this->layoutStack);
    }
}
