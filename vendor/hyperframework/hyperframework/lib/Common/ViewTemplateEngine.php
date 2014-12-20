<?php
namespace Hyperframework\Common;

use ArrayAccess;
use Exception;
use Closure;

abstract class ViewTemplateEngine implements ArrayAccess {
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

    public function load($path) {
        $path = (string)$path;
        if ($path === '') {
            throw new Exception;
        }
        $extensionPattern = '#\.[.0-9a-zA-Z]+$#';
        if (preg_match($extensionPattern, $path, $matches) === 0) {
            if ($this->fullPath === null) {
                throw new Exception;
            }
            preg_match($extensionPattern, $this->fullPath, $matches);
            $path .= $matches[0];
        }
        $this->pushContext();
        $this->setLayout(null);
        if (isset($options['root_path'])) {
            $this->setRootPath($options['root_path']);
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        if ($path[0] === DIRECTORY_SEPARATOR) {
            throw new Exception;
        }
        $this->fullPath = $this->getRootPath() . DIRECTORY_SEPARATOR . $path;
        $includeFileFunction = $this->includeFileFunction;
        $includeFileFunction($this->fullPath);
        if ($this->layoutPath !== null) {
            $this->setRootPath($this->layoutRootPath);
            $this->load($this->layoutPath);
        }
        $this->popContext();
    }

    public function setLayout($path) {
        $this->layoutPath = $path;
        $this->layoutRootPath = $this->getRootPath();
    }

    protected function getFullPath() {
        return $this->fullPath;
    }

    protected function renderBlock($name, Closure $default = null) {
        $this->pushContext();
        if (isset($this->blocks[$name])) {
            $function = $this->blocks[$name]['function'];
            $this->setRootPath($this->blocks[$name]['root_path']);
            $this->fullPath = $this->blocks[$name]['full_path'];
            $function();
        } else {
            if ($default === null) {
                throw new Exception("View block '$name' not found");
            }
            $default();
        }
        $this->popContext();
    }

    protected function setBlock($name, Closure $function) {
        $this->blocks[$name] = [
            'function' => $function,
            'root_path' => $this->rootPath,
            'full_path' => $this->fullPath,
        ];
    }

    public function getRootPath() {
        if ($this->rootPath === null) {
            $path = Config::getString('hyperframework.web.view.root_path', '');
            if ($path === '') {
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
