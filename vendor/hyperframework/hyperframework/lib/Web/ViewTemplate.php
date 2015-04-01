<?php
namespace Hyperframework\Web;

use ArrayAccess;
use InvalidArgumentException;
use Exception;
use Hyperframework\Common\Config;
use Hyperframework\Common\FileFullPathBuilder;
use Hyperframework\Common\FileFullPathRecognizer;
use Hyperframework\Common\FilePathCombiner;

abstract class ViewTemplate implements ArrayAccess {
    private $viewModel;
    private $loadFileFunction;
    private $blocks = [];
    private $layoutPathStack = [];
    private $rootPath;
    private $filePath;
    private $layoutPath;

    public function __construct($loadFileFunction, $viewModel = null) {
        $this->loadFileFunction = $loadFileFunction;
        $this->viewModel = $viewModel === null ? [] : $viewModel;
    }

    public function render($path) {
        $path = (string)$path;
        if ($path === '') {
            throw new ViewException('View path cannot be empty.');
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        if (FileFullPathRecognizer::isFullPath($path)) {
            $this->filePath = $path;
        } else {
            FilePathCombiner::prepend($path, $this->getRootPath());
            $this->filePath = $path;
        }
        $this->pushLayout();
        try {
            $loadFileFunction = $this->loadFileFunction;
            $loadFileFunction();
            $this->filePath = null;
            if ($this->layoutPath !== null) {
                $this->render($this->layoutPath);
            }
        } catch (Exception $e) {
            $this->popLayout();
            throw $e;
        }
        $this->popLayout();
    }

    public function setLayout($path) {
        $this->layoutPath = $path;
    }

    public function getFilePath() {
        return $this->filePath;
    }

    public function renderBlock($name, $default = null) {
        if (isset($this->blocks[$name])) {
            $block = $this->blocks[$name];
            $block();
        } else {
            if ($default === null) {
                throw new ViewException("Block '$name' does not exist.");
            }
            $default();
        }
    }

    public function setBlock($name, $value) {
        $this->blocks[$name] = $value;
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new InvalidArgumentException(
                "Argument 'offset' cannot be null."
            );
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
            $this->rootPath = FileFullPathBuilder::build($path);
        }
        return $this->rootPath;
    }

    private function pushLayout() {
        array_push($this->layoutPathStack, $this->layoutPath);
        $this->setLayout(null);
    }

    private function popLayout() {
        $this->layoutPath = array_pop($this->layoutPathStack);
    }
}
