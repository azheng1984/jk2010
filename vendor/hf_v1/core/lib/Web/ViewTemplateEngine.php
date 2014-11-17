<?php
namespace Hyperframework\Web;

use ArrayAccess;
use Exception;
use Closure;
use Hyperframework;
use Hyperframework\FullPathRecognizer;
use Hyperframework\Config;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $includeFileFunction;
    private $contextStack = [];
    private $isParent = false;
    private $isBlock = false;
    private $rootPath;
    private $fullPath;
    private $layout;
    private $blocks;

    public function __construct($includeFileFunction, array $model = null) {
        $this->includeFileFunction = $includeFileFunction;
        $this->model = $model === null ? [] : $model;
    }

    public function load($path, array $options = []) {
        if ($path == '') {
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
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        if ($path[0] !== DIRECTORY_SEPARATOR) {
            if ($this->fullPath === null) {
                throw new Exception;
            }
            $tmps = explode(DIRECTORY_SEPARATOR, $this->fullPath);
            array_pop($tmps);
            implode(DIRECTORY_SEPARATOR, $tmps) . DIRECTORY_SEPARATOR . $path;
        } else {
            $path = $this->getRootPath() . DIRECTORY_SEPARATOR . $path;
        }
        $context['root_path'] = $this->rootPath;
        $context['full_path'] = $this->fullPath;
        $context['layout'] = $this->layout;
        $isParent = $this->isParent;
        if ($isParent === false) {
            $context['blocks'] = $this->blocks;
            $this->blocks = [];
        }
        array_push($this->contextStack, $context);
        $this->fullPath = $path;
        $this->opitons = $options;
        if (isset($options['layout'])) {
            $this->layout = $options['layout'];
        }
        $includeFileFunction = $this->includeFileFunction;
        $includeFileFunction($this->fullPath);
        if (isset($this->options['layout'])) {
            $this->isParent = true;
            $this->load($this->options['layout']);
            $this->isParent = false;
        }
        $context = array_pop($this->contextStack);
        $this->rootPath = $context['root_path'];
        $this->fullPath = $context['full_path'];
        $this->layout = $context['layout'];
        if ($isParent) {
            $this->blocks = $context['blocks'];
        }
    }

    protected function getFullPath() {
        return $this->fullPath;
    }

    protected function extend($layout) {
        if ($this->isBlock) {
            throw new Exception;
        }
        $this->layout = $layout;
    }

    protected function renderBlock($name, Closure $default = null) {
        //push context
        if (isset($this->blocks[$name])) {
            $function = $this->blocks[$name];
            $function();
        } else {
            if ($default === null) {
                throw new Exception("View block '$name' not found");
            }
            $default();
        }
        //pop context
    }

    protected function setBlock($name, Closure $function) {
        $this->blocks[$name] = [[
            'function' => $function,
            'root_path' => $this->rootPath,
            'full_path' => $this->fullPath
        ]];
    }

    protected function appendBlock($name, Closure $function) {
    }

    protected function prependBlock($name, Closure $function) {
    }

    protected function getRootPath() {
        if ($this->rootPath === null) {
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
            $this->rootPath = $path;
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
}
