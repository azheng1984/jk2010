<?php
namespace Hyperframework\Web;

use Hyperframework\FullPathRecognizer;

class ViewTemplate extends ViewTemplateEngine {
    private $pathStack = [];
    private $optionStack = [];

    public function render($path, array $options = []) {
        $parentPath = null;
        if (count($this->pathStack) !== 0) {
            $parentPath = end($this->pathStack);
        }
        if ($path === null || $path == '') {
            throw new Exception;
        }
        if ($path[0] !== '/') {
            if ($parentPath === null) {
                throw new Exception;
            }
            $tmp = explode('/', $parentPath);
            $file = array_pop($tmp);
            $path = implode('/', $tmp) . $path;
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
        unset($path);
        unset($options);
        include $this->getViewRootPath() . end($this->pathStack);
        $options = array_pop($this->optionStack);
        if (isset($options['layout'])) {
            $this->renderLayout($options['layout']);
        }
    }

    private function extend($layout) {
        $options = array_pop($this->optionStack);
        $options['layout'] = $layout;
        array_push($this->optionStack, $options);
    }
}
