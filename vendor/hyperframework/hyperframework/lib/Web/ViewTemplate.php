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
    private $model;
    private $includeFileFunction;
    private $blocks = [];
    private $contextStack = [];
    private $rootPath;
    private $fullPath;
    private $layoutPath;
    private $layoutRootPath;

    public function __construct($includeFileFunction, array $model = null) {
        $this->includeFileFunction = $includeFileFunction;
        $this->model = $model === null ? [] : $model;
    }

    public function render($path) {
        $path = (string)$path;
        if ($path === '') {
            throw new InvalidArgumentException('View path cannot be empty.');
        }
        $extensionPattern = '#\.[.0-9a-zA-Z]+$#';
        if (preg_match($extensionPattern, $path, $matches) === 0) {
            if ($this->fullPath === null) {
                throw new InvalidArgumentException(
                    "Path '$path' is invalid, file extension does not exist."
                );
            }
            preg_match($extensionPattern, $this->fullPath, $matches);
            $path .= $matches[0];
        }
        $this->pushContext();
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
        $includeFileFunction = $this->includeFileFunction;
        $includeFileFunction($this->fullPath);
        if ($this->layoutPath !== null) {
            $this->setRootPath($this->layoutRootPath);
            $this->render($this->layoutPath);
        }
        $this->popContext();
    }

    public function setLayout($path) {
        $this->layoutPath = $path;
        $this->layoutRootPath = $this->getRootPath();
    }

    public function getFullPath() {
        return $this->fullPath;
    }

    public function renderBlock($name, Closure $default = null) {
        $this->pushContext();
        if (isset($this->blocks[$name])) {
            $function = $this->blocks[$name]['function'];
            $this->setRootPath($this->blocks[$name]['root_path']);
            $this->fullPath = $this->blocks[$name]['full_path'];
            $function();
        } else {
            if ($default === null) {
                throw new ViewException("Block '$name' does not exist.");
            }
            $default();
        }
        $this->popContext();
    }

    public function setBlock($name, Closure $function) {
        $this->blocks[$name] = [
            'function' => $function,
            'root_path' => $this->rootPath,
            'full_path' => $this->fullPath,
        ];
    }

    public function getRootPath() {
        if ($this->rootPath === null) {
            $path = Config::getString('hyperframework.web.view_path.root');
            if ($path === null) {
                $path = 'views';
            }
            $this->rootPath = FileLoader::getFullPath($path);
        }
        return $this->rootPath;
    }

    public function setRootPath($value) {
        $this->rootPath = $value;
    }

    public function __invoke($function) {
        $function();
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new InvalidArgumentException('Offset cannot be null.');
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

    private function pushContext() {
        $context = [
            'root_path' => $this->rootPath,
            'full_path' => $this->fullPath,
            'layout_path' => $this->layoutPath,
            'layout_root_path' => $this->layoutRootPath
        ];
        array_push($this->contextStack, $context);
    }

    private function popContext() {
        $context = array_pop($this->contextStack);
        $this->rootPath = $context['root_path'];
        $this->fullPath = $context['full_path'];
        $this->layoutPath = $context['layout_path'];
        $this->layoutRootPath = $context['layout_root_path'];
    }
}
