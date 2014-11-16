<?php
namespace Hyperframework\Web;

use Hyperframework\FullPathRecognizer;

class ViewTemplate extends ViewTemplateEngine {
    private $path;
    private $optionStack = [];

    public function render($path, array $options = []) {
        if (FullPathRecognizer::isFull($path) === false) {
            $path = $this->getViewRootPath() . DIRECTORY_SEPARATOR . $path;
        }
        $this->path = $path;
        $this->optionStack[] = $options;
        unset($path);
        unset($options);
        include $this->path;
        $options = array_pop($this->optionStack);
        if (isset($options['layout'])) {
            $this->renderLayout($options['layout']);
        }
    }

    private function setLayout($value) {
        $options = array_pop($this->optionStack);
        $options['layout'] = $value;
        array_push($this->optionStack, $options);
    }
}
