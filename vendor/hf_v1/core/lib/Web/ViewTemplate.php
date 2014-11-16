<?php
namespace Hyperframework\Web;

use Hyperframework\FullPathRecognizer;

class ViewTemplate extends ViewTemplateEngine {
    public function render($path, array $options = []) {
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
        unset($path);
        unset($options);
        include $this->getViewRootPath() . DIRECTORY_SEPARATOR
            . end($this->pathStack);
        $options = array_pop($this->optionStack);
        if (isset($options['layout'])) {
            $this->render($options['layout']);
        }
    }

    private function extend($layout) {
        $options = array_pop($this->optionStack);
        $options['layout'] = $layout;
        array_push($this->optionStack, $options);
    }
}
