<?php
namespace Hyperframework\Web;

use ArrayAccess;
use Exception;
use Hyperframework;
use Hyperframework\FullPathRecognizer;
use Hyperframework\Config;

abstract class ViewTemplateEngine implements ArrayAccess {
    private $model;
    private $includeFileFunction;
    private $fullPath;
    private $viewRootPath;
    private $blocksStack = [];
    private $pathStack = [];
    private $optionStack = [];

    public function __construct($includeFileFunction, array $model) {
        $this->includeFileFunction = $includeFileFunction;
        $this->model = $model;
    }

    public function load($path, array $options = []) {
        $parentPath = null;
        if (count($this->pathStack) !== 0) {
            $parentPath = end($this->pathStack);
        }
        if ($path === null || $path == '') {
            throw new Exception;
        }
        $extensionPattern = '#\.[.0-9a-zA-Z]+$#';
        if (preg_match($extensionPattern, $path, $matches) === 0) {
            if ($parentPath === null) {
                throw new Exception;
            }
            preg_match($extensionPattern, $parentPath, $matches);
            $path .= $matches[0];
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        $this->pathStack[] = $path;
        $this->optionStack[] = $options;
        $this->fullPath = $this->getViewRootPath()
            . DIRECTORY_SEPARATOR . end($this->pathStack);
        $includeFileFunction = $this->includeFileFunction;
        $includeFileFunction();
        $options = array_pop($this->optionStack);
        if (isset($options['layout'])) {
            $this->load($options['layout'], ['blocks' => $this->getBlocks()]);
        }
    }

    protected function getFullPath() {
        return $this->fullPath;
    }

    protected function extend($layout) {
        $options = array_pop($this->optionStack);
        $options['layout'] = $layout;
        array_push($this->optionStack, $options);
    }

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

    private function pushOptions($options) {
    }

    private function popOptions() {
    }

    private function getOptions() {
    }

    protected function setBlock($name, $function) {
        //$this->blocks[$name] = array($function, $this->getPath());
        $this->blocks[$name] = $function;
    }

    private function getBlocks() {
        return $this->blocks;
    }

    private function pushBlocks($blocks) {
        $this->blocks[] = $blocks;
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

    public function setViewRootPath($value) {
        $this->viewRootPath = $value;
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
