<?php
namespace Hyperframework\Web;

use ArrayAccess;
use InvalidArgumentException;
use Exception;
use Closure;
use Hyperframework\Common\Config;
use Hyperframework\Common\FileFullPathBuilder;
use Hyperframework\Common\FileFullPathRecognizer;
use Hyperframework\Common\FilePathCombiner;

abstract class ViewKernel implements ArrayAccess {
    private $viewModel;
    private $loadFileFunction;
    private $blocks = [];
    private $layoutPathStack = [];
    private $rootPath;
    private $file;
    private $layoutPath;

    /**
     * @param array $viewModel
     */
    public function __construct(array $viewModel = null) {
        $this->loadFileFunction = Closure::bind(function () {
            require $this->getFile();
        }, $this, null);
        $this->viewModel = $viewModel === null ? [] : $viewModel;
    }

    /**
     * @param string $path
     */
    public function render($path) {
        $path = (string)$path;
        if ($path === '') {
            throw new ViewException('View path cannot be empty.');
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        if (FileFullPathRecognizer::isFullPath($path)) {
            $this->file = $path;
        } else {
            FilePathCombiner::prepend($path, $this->getRootPath());
            $this->file = $path;
        }
        $this->pushLayout();
        try {
            $loadFileFunction = $this->loadFileFunction;
            $loadFileFunction();
            $this->file = null;
            if ($this->layoutPath !== null) {
                $this->render($this->layoutPath);
            }
        } catch (Exception $e) {
            $this->popLayout();
            throw $e;
        }
        $this->popLayout();
    }

    /**
     * @param string $path
     */
    public function setLayout($path) {
        $this->layoutPath = $path;
    }

    /**
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @param string $name
     * @param Closure $default
     */
    public function renderBlock($name, Closure $default = null) {
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

    /**
     * @param string $name
     * @param Closure $value
     */
    public function setBlock($name, Closure $value) {
        $this->blocks[$name] = $value;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new InvalidArgumentException(
                "Argument 'offset' cannot be null."
            );
        } else {
            $this->viewModel[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->viewModel[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->viewModel[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if (isset($this->viewModel[$offset]) === false) {
            throw new ViewException(
                "View model field '$offset' is not defined."
            );
        }
        return $this->viewModel[$offset];
    }

    /**
     * @return string
     */
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
